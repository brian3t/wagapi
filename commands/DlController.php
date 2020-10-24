<?php

namespace app\commands;

require_once dirname(__DIR__) . "/yii2helper/PHPHelper.php";

use app\models\Band;
use app\models\BandEvent;
use app\models\Event;
use app\models\Venue;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\DomCrawler\Crawler;
use usv\yii2helper\PHPHelper;
use Yii;
use yii\console\Controller;

define('SDRCOM', 'https://www.sandiegoreader.com/');
define('SDREADER', 'https://www.sandiegoreader.com/events/search/?category=Genre');//&start_date=2020-06-01&end_date=2020-07-01
define('SDREADER_LOCAL', 'http://lnoapi/scrape/Eventsearch_SanDiegoReader.html');
define('SDREVENT_LOCAL', 'http://lnoapi/scrape/gingercowgirl.html');
define('SDRBAND_LOCAL', 'http://lnoapi/scrape/band_gg_cowgirl.html');
define('TICKMAS', 'https://www.ticketmaster.com/api/next/graphql?operationName=CategorySearch&extensions={"persistedQuery":{"version":1,"sha256Hash":"e6feb139aaeaa5a2bbcf9a37e6ee6bb29cca1d6dce85fb7746f25f859041c2b4"}}');

define('DELAY', 30);//delay sec b/w http req
define('MAX_SCRAPE_PER_DAY', 12);//stop if we reach this num per day

/**
 * The behind the scenes magic happens here
 *
 * @author Brian Nguyen
 */
class DlController extends Controller
{
    public $SCRAPER_ID = null;
    public $opt;

    public function init()
    {
        $this->SCRAPER_ID = \Yii::$app->params['scraper_id'];
        parent::init();
    }

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'opt'
        ]);
    }

    public function actionScrapeSdrAll()
    {
        $this->actionScrapeSdr();
        $this->actionPullEventSdr();
        $this->actionVenueAddrSdr();
    }

    public function actionScrapeReverbAll()
    {
        $this->actionScrapeReverb();
        $this->actionPullBandReverb();
    }

    /**
     * Pull events from SDReader
     * @param int $days_forward lookahead
     * @throws \Exception
     */
    public function actionScrapeSdr($days_forward = 1)
    {
        $date = (new \DateTime())->add(new \DateInterval("P{$days_forward}D"));
        $opt = json_decode($this->opt, true);
        $date_str = $date->format('Y-m-d');
        $records = 0;
        $client = new Client();
        $event_client = new Client();
        $band_client = new Client();
        $is_debug = isset($opt['debug']) && $opt['debug'];
        $is_local = isset($opt['local']) && $opt['local'];
        $is_logging = isset($opt['log']) && $opt['log'];
        if ($is_local) {
            $raw_html = file_get_contents(dirname(__DIR__) . "/web/scrape/ig_sdr_live.html");
            $crawler = new Crawler($raw_html);
        } else {
            $crawler = $client->request('GET', SDREADER, ['start_date' => $date_str, 'end_date' => $date_str]);
            $raw_html = $crawler->html();
            file_put_contents(dirname(__DIR__) . "/web/scrape/ig_sdr_live.html", $raw_html);
        }
        //6/29/20
//        $crawler = $client->request('GET', SDREADER_LOCAL, ['start_date' => '2018-07-04', 'end_date' => '2018-07-04']);
        //first, go by date
        $crawler->filter('div.events-date')->each(function ($ev_per_date) use (&$records, $date_str, $event_client, $band_client, $opt, $crawler, $is_debug, $is_logging) {
            $ev_date_str = $ev_per_date->filter('h2')->text();//Friday, Aug. 3, 2020
            $ev_date_str = str_replace('.', '', $ev_date_str);
            $ev_date = \DateTime::createFromFormat('l, F j, Y', $ev_date_str, new \DateTimeZone('America/Los_Angeles'));
            if ($ev_date instanceof \DateTime) {
                $ev_date_utc = clone($ev_date);
            } else {
                $ev_date_utc = new \DateTime();
                $ev_date = new \DateTime();
            }
            $ev_date_utc->setTimezone(new \DateTimeZone('UTC'));
            $ev_per_date->filter('div.event-item')->each(function ($event_and_venue) use (&$records, $date_str, $event_client, $band_client, $opt, $ev_date, $ev_date_utc, $is_logging) {
                if (isset($opt['debug']) && $opt['debug']) {
                    //dont delay
                } else {
                    sleep(random_int(2, DELAY));
                }
                /** @var Crawler $event_and_venue */
                [$event_name, $event_href] = current($event_and_venue->filter('a.event-title')->extract(['_text', 'href']));
                if ($is_logging) echo "event name: $event_name . ";
                if (empty($event_name)) {
                    return;
                }

                $venue_name = $event_and_venue->filter('a.event-place > div.event-location')->text();
                $venue_href = current($event_and_venue->filter('a.event-place')->extract(['href']));
                //see if it has local artist page
                $event_crawler = $event_client->request('GET', SDRCOM . $event_href);
                $h4_local_artist = $event_crawler->filter('h4:contains("Local artist page:")');
                $has_local_artist = $h4_local_artist->count() > 0;
                if ($is_logging) echo "venue name: ". $venue_name . " . ";
                if (! $venue_name || empty($venue_name)) {
                    return;
                }
                //find out if venue already exists
                $venue_exist = Venue::findOne(['name' => $venue_name]);
                if (! $venue_exist instanceof Venue) {
                    $venue = new Venue();
                    $venue->setAttributes(['name' => $venue_name,
                        'sdr_name' => str_replace('https://www.sandiegoreader.com', '', $venue_href),
                        'system_note' => $venue_href]);//'https://www.sandiegoreader.com' .
                    $venue->county = 'San Diego';
                    $venue->source = 'sdr';
                    $venue->state = 'CA';
                    $city_el = $event_and_venue->filter('a.event-place + a');
                    if (is_object($city_el) && $city_el->count()) $venue->city = $city_el->text();
                    $venue->save();
                    $venue_id = $venue->id;
                } else {
                    $venue_id = $venue_exist->id;
                }
                if (! is_int($venue_id)) {
                    return;//when failed saving new venue / pulling existing venue
                }
                Yii::$app->db->createCommand("UPDATE venue SET `created_by` = :created_by WHERE `id` = :venue_id", [
                    ':created_by' => $this->SCRAPER_ID,
                    ':venue_id' => $venue_id
                ])->execute();
                $records++;
                //find out if event already exists
                $event = new Event();
                $event_exist = Event::findOne(['name' => $event_name]);
                if (! $event_exist instanceof Event) {
                    $event = new Event();
                    $event->setAttributes(['created_by' => $this->SCRAPER_ID, 'name' => $event_name, 'source' => 'sdr',
                        'sdr_name' => str_replace('https://www.sandiegoreader.com/', '', $event_href), 'system_note' => $event_href]);//'https://www.sandiegoreader.com/' .
                    $ev_time = $event_and_venue->filter('div.event-time');
                    if ($ev_time instanceof Crawler) {
                        $ev_time_str = $ev_time->text();//8pm
                        $ev_time = \DateTime::createFromFormat('h:ia', $ev_time_str);//hour and minute, such as 8:30pm
                        if ($ev_time === false) $ev_time = \DateTime::createFromFormat('ha', $ev_time_str); //hour only, such as 8pm
                        if ($ev_time instanceof \DateTime) {
                            $ev_time_utc = clone($ev_time);
                        } else $ev_time_utc = new \DateTime();
                        $ev_time_utc->setTimezone(new \DateTimeZone('UTC'));
                    }
                    /* format for venue page
                    $date_and_time = $event_and_venue->filter('div.event-date-time')->text();// Friday, July 3, 2020, 8 p.m. //moment('dddd MMMM D YYYY h a')
                    $date_and_time = \DateTime::createFromFormat('dddd MMMM D YYYY h a', $date_and_time);
                    */
                    //$city = $event_and_venue->filter('td.city>ul>li>a')->text();
//                        $short_desc = implode(', ', $event_and_venue->filter('td.category > ul li')->extract(['_text']));
                    $event->venue_id = $venue_id;
                    $event->date = $ev_date->format('Y-m-d');
                    $event->date_utc = $ev_date_utc->format('Y-m-d');
                    if ($ev_time instanceof \DateTime) {
                        $event->start_time = $ev_time->format('H:i:s');
                        $event->start_time_utc = $ev_time_utc->format('H:i:s');
                    }
                    $img = $event_and_venue->filter('div.event-avatar')->extract(['style']);
                    $img = array_filter($img, function ($style) {
                        return strpos($style, 'background-image') !== FALSE;
                    });
                    $img = array_pop($img);//background-image: url('https://media.sandiegoreader.com/img/events/2020/tempLong_Run_t150.jpg?9327a3fb59f61056fdcd01aa32ef3b74a9932e1d');
                    if (! empty($img)) {
                        $img = str_replace(['background-image: url(\'', '\');'], '', $img);
                    }
                    $event->img = $img;
//                        $event->setAttributes(compact(['time', 'city', 'short_desc']));
                    $event->save();
                    if ($is_logging) echo "event saved: " . json_encode($event->attributes). '\n';
                    $event_id = $event->id;
                    if (is_int($event_id)) {
                        Yii::$app->db->createCommand("UPDATE event SET `created_by` = :created_by WHERE `id` = :id", [
                            ':created_by' => $this->SCRAPER_ID,
                            ':id' => $event_id
                        ])->execute();
                    }
                    $records++;
                } else {
                    $event_id = $event->id;
                }
                /*//saving band info too
                $band_href = $h4_local_artist->nextAll()->filter('div.image_grid a')->attr('href');
                $band_crawler = $band_client->request('GET', SDRCOM . $band_href);
//            $band_crawler = $band_client->request('GET', SDRBAND_LOCAL);
                $band_content = $band_crawler->filter('#content');
                $name = $band_content->filter('div.content_title > h2')->text();
                if (empty($nampase)) {
                    return;
                }
                try {
                    $name = strtolower($name);
                    $hometown_city = 'San Diego';
                    $hometown_state = 'CA';
                    $logo = $band_content->filter('img.lead_photo')->attr('src');
                    if (empty($logo)) {
                        return;
                    }
                    $genre = $band_content->filter('strong:contains("Genre:")')->parents()->text();
                    $genre = strtolower(str_replace(['Genre: ', ', '], ['', ','], $genre));
                    $similar_to = $band_content->filter('strong:contains("RIYL:")')->parents()->text();
                    $similar_to = strtolower(str_replace(['RIYL: ', ', '], ['', ','], $similar_to));
                    $description = $band_content->filter('h3#history')->parents()->text();
                    $related = $band_content->filter('h3#related')->parents();
                    $website = $related->filter('a:contains("website")')->attr('href');
                    $facebook = $related->filter('a:contains("Facebook")')->attr('href');
                } catch (\Exception $e) {
                    $band_content = null;
                    $logo = null;
                    $genre = null;
                    $similar_to = null;
                    $description = null;
                    $website = null;
                    $facebook = null;
                }
                $band = Band::findOne(['name' => $name]);
                if (! $band instanceof Band) {
                    $band = new Band();
                    $band->setAttributes(compact(['name', 'logo', 'genre', 'similar_to', 'hometown_city', 'hometown_state', 'description', 'website', 'facebook']));
                    $band->type = 'originals';
                    $band->lno_score = random_int(5, 10);
                    $band->source = 'sdr';
                    $band->save();
                }
                $band_id = $band->id;
                if (is_int($band_id) && is_int($event_id)) {
                    $band_event = new BandEvent();
                    $band_event->band_id = $band_id;
                    $band_event->event_id = $event_id;
                    $band_event->save();
                }*/
            });
            echo "| $records \n";
            if ($is_debug && $records > 0) die();
        });
        echo "Pulled this much: " . $records . " records." . PHP_EOL;
    }

    /**
     * Pull address for all venues without address
     */
    public function actionVenueAddrSdr()
    {
        $updated = 0;
        $venues_wo_addr = Venue::find()->where(['not', ['sdr_name' => null]])->andWhere(['address1' => null])->all();
        $venue_client = new Client();
        foreach ($venues_wo_addr as $venue_wo_addr) {
            $sdr_url = SDRCOM . $venue_wo_addr->sdr_name;
            $sdr_url = str_replace(SDRCOM . SDRCOM, SDRCOM, $sdr_url);//remove duplicates
            $crawler = $venue_client->request('GET', $sdr_url);
            $type_a = $crawler->filter('ul.categories > li:first-child > a');
            if ($type_a instanceof Crawler) {
                $type = $type_a->text();
            } else {
                $type = null;
            }
            $addr_a = $crawler->filter('a:contains("Directions")');
            if ($addr_a instanceof Crawler) {
                $addr = $addr_a->parents()->text();
            }
            if (empty($addr)) {
                return false;
            }
            $addr = str_replace(' | Directions', '', $addr);
            $addr = str_replace(' (NCC)', '', $addr);
            $address_variables = PHPHelper::parseAddress($addr);
            $venue_wo_addr->setAttributes($address_variables);
            if ($venue_wo_addr->save()) {
                $updated++;
            }
        }
        echo "Updated $updated rows\n";
        return true;
    }

    /**
     * Pull event info from SDR events
     */
    public function actionPullEventSdr()
    {
        $client = new Client();
        $events_sdr = Event::find()->where(['source' => 'sdr'])->andFilterWhere(['or', ['img' => null], ['img' => '']])
//            ->andWhere(['>=', 'updated_at', new Expression('DATE_SUB(CURDATE(), INTERVAL 7 DAY)')])
            ->all();
        foreach ($events_sdr as $event_sdr) {
//            $crawler = $client->request('GET', SDREADER, ['start_date' => $date_str, 'end_date' => $date_str]);
//            $crawler = $client->request('GET', 'http://lnoapi/scrape/Opera%20Appreciation%20Class%20_%20San%20Diego%20Reader.html');//local
            $crawler = $client->request('GET', $event_sdr->system_note);
            $content_info = $crawler->filter('div.content_info');
            $img = null;
            try {
                $img = $content_info->filter('div.thumbnail-container > img')->attr('src');
            } catch (\Exception $exception) {
            }
            $img = preg_replace('/\?[a-z0-9]+/', '', $img);//https://media.sandiegoreader.com/img/events/2017/images_3_t240.jpg?abc123
            $description = null;
            try {
                $description = $content_info->filter('div.thumbnail-container +div')->text();
            } catch (\Exception $exception) {
            }
            $cost = null;
            try {
                $cost = $content_info->filter('ul.details > li:first-child')->text();
            } catch (\Exception $e) {
            }
            if (strpos($cost, 'Cost:') !== false) {
                $cost = trim(str_replace('Cost:', '', $cost));
                $cost = preg_replace('/\n\| Website/', '', $cost);
                $cost = str_replace('$', '', $cost);
            } else {
                $cost = null;
            }
            $age_limit = null;
            try {
                $age_limit = $content_info->filter('ul.details > li:nth-child(2)')->text();
            } catch (\Exception $e) {
            }
            if (strpos($age_limit, 'Age limit:') !== false) {
                $age_limit = trim(str_replace('Age limit:', '', $age_limit));
            } else {
                $age_limit = null;
            }
            $when = null;
            try {
                $when = $content_info->filter('ul.details > li:nth-child(3)')->text();
            } catch (\Exception $e) {
            }
            if (strpos($when, 'When:') !== false) {
                $when = trim(str_replace('When:', '', $when));
            } else {
                $when = null;
            }
            $event_sdr->setAttributes(compact('img', 'description', 'cost', 'age_limit', 'when'));
            $event_sdr->save();
        }
    }

    public function actionScrapeReverbAllcities()
    {
        require_once dirname(__DIR__) . "/config/reverb/constant.php";
        $location = BASE_LOC_ARRAY;
        foreach (LOCATIONS as $LOCATION) {
            $location['state'] = $LOCATION[0];
            $location['city'] = $LOCATION[1];
            $location['postal_code'] = $LOCATION[2];
            $this->actionScrapeReverb($location);
        }

    }

    /**
     * Scrape from ReverbNation
     * @param $location array e.g. San Diego
     * @param $per_page int per page
     * @return boolean result
     * @throws
     */
    public function actionScrapeReverb($location = ["geo" => "local", "country" => "US", "state" => "CA", "city" => "San%20Diego", "postal_code" => "92115"], $per_page = 10)
    {

//        $IS_DEBUG = true;
        $IS_DEBUG = false;
        $scraped = 0;
        $url = "https://www.reverbnation.com/main/local_scene_data?page=1&range={\"type\":\"full\",\"date\":\"\"}";
        if ($IS_DEBUG) {
            $events = file_get_contents(dirname(__DIR__) . "/web/scrape/reverb_ev.json");
        } else {
            $params = ['per_page' => $per_page, 'location' => json_encode($location)];
            $url .= "&" . http_build_query($params);
            $guzzle = new GuzzleClient();
            $events = $guzzle->request('GET', $url, $params);
            if ($events->getStatusCode() !== 200) {
                echo 'Failed. ' . $events->getStatusCode();
                return false;
            }
            $events = $events->getBody();
            if (! method_exists($events, 'getContents')) {
                echo 'Bad response. Url: ' . $url;
                return false;
            }
            $events = $events->getContents();
        }
        $events = json_decode($events);

        if (isset($events->shows) && is_array($events->shows)) {
            foreach ($events->shows as $show) {
//                var_dump($show);
                $venue = Venue::findOrCreate(['name' => $show->venue_name]);
                /** @var $venue Venue */
                $venue->created_by = $this->SCRAPER_ID;
                $venue->source = 'reverb';
                $event_columns = ['venue_id' => $venue->id];
                $show_time = \DateTime::createFromFormat('Y/m/d H:i:s', $show->showtime);
                $event_columns['date'] = $show_time->format('Y-m-d');
                $event_columns['start_time'] = $show_time->format('h:i:s');
                $event = Event::findOrCreate($event_columns);
                $event->source = 'reverb';
                /** @var Event $event */
                $venue_attrs = ['venue_link' => $show->venue_link, 'show_id' => $show->show_id, 'artists' => json_encode($show->artists)];
                $venue->city = $show->city;
                $venue->state = $show->state;
                $venue->attr = $venue_attrs;
                try {
                    $venue->save();
                } catch (\Exception $exception) {
                    Yii::error($exception);
                    continue;
                }
                if ($venue->errors) {
                    Yii::error($venue->errors);
                }
                $event->img = $show->image_url;
                $event->venue_id = $venue->id;
                $event->name = $venue->name;
                $event->date = $event_columns['date'];
                try {
                    $event->save();
                } catch (\Exception $exception) {
                    Yii::error($exception);
                    continue;
                }
                //now parse bands
                foreach ($show->artists as $artist) {
                    $band_columns = ['name' => $artist->name];
                    $band = Band::findOrCreate($band_columns);
                    /** @var Band $band */
                    $band->source = 'reverb';
                    $band->attr = ['id' => $artist->id, 'url' => $artist->url];
                    try {
                        $band->save();
                    } catch (\Exception $e) {
                        Yii::error($e);
                        continue;
                    }
                    if ($band->errors) {
                        Yii::error($band->errors);
                    }
                    $band_event = new BandEvent();
                    $band_event->band_id = $band->id;
                    $band_event->event_id = $event->id;
                    $band_event->created_by = $this->SCRAPER_ID;
                    try {
                        $band_event->save();
                    } catch (\Exception $exception) {
                        //ignore. probably duplicate band_event
                    }
                }
                $scraped++;
//                echo "Pulled: $scraped events";
            }
        }
        echo "Pulled: $scraped events";
        return false;
    }

    /**
     * Scrape from Ticket Master
     * @param $variables array
     * @param $per_page int per page
     * @return boolean result
     * @throws
     */
    public function actionScrapeTickmas($per_page = 10, $variables = [])
    {
//        $IS_DEBUG = true;
        $IS_DEBUG = false;
        $today = new \DateTime();
        $today_str = $today->format('Y-m-d');
        $nextweek_str = $today->add(new \DateInterval('P7D'))->format('Y-m-d');
        $timerange = "${today_str}T00:00:00,${nextweek_str}T23:59:59";
        $scraped = 0;
        $guzzle = new GuzzleClient();
        $page = 0;
        $scrape_next_page = false;
        do {
            $todaytime_str = (new \DateTime())->format('Ymd_His');
            if ($IS_DEBUG) {
                $events = file_get_contents(dirname(__DIR__) . "/web/scrape/tickmas.json");
            } else {
                $params = ['variables' => json_encode(["locale" => "en-us",
                    "sort" => "date,asc",
                    "page" => $page,
                    "size" => $per_page,
                    "lineupImages" => true,
                    "includeDateRange" => true,
                    "withSeoEvents" => true,
                    "radius" => "50",
                    "geoHash" => "9mud",
                    "unit" => "miles",
                    "segmentId" => "KZFzniwnSyZfZ7v7nJ",
                    "localeStr" => "en-us",
                    "type" => "event",
                    "localStartEndDateTime" => $timerange])
                ];
                $url = TICKMAS;
                $url .= "&variables=${params['variables']}";
                $events = $guzzle->request('GET', $url, $params);
                if ($events->getStatusCode() !== 200) {
                    echo 'Failed. ' . $events->getStatusCode();
                    return false;
                }
                $events = $events->getBody();
                if (! method_exists($events, 'getContents')) {
                    echo 'Bad response. Url: ' . $url;
                    return false;
                }
                $events = $events->getContents();
                $raw_file = dirname(__DIR__) . "/web/scrape/tickmas/rawdl_${todaytime_str}.json";
//                file_put_contents($raw_file, $events); //zsdf
            }
            $events = json_decode($events);
            if (! property_exists($events, 'data')) {
                echo 'events no data';
                return false;
            }
            $events = $events->data;
            if (! property_exists($events, 'products')) {
                echo 'events data no products';
                return false;
            }
            $events = $events->products;
            $page_count = $events->page->totalPages ?? null;
            $page_count--;
            if (is_int($page_count) && ($page < $page_count)) {
                $page = intval($page + 1);
                $scrape_next_page = true;
            } else {
                $scrape_next_page = false;
            }
            if (! is_object($events) || ! property_exists($events, 'items')) {
                echo 'products not obj or products no items';
                return false;
            }
            $events = $events->items;
            if (! is_array($events)) {
                echo 'items not array';
                return false;
            }
            foreach ($events as $e) {
                $em = Event::findOrCreate(['name' => $e->name]);
                /** @var $em Event */
                $em->created_by = $this->SCRAPER_ID;
                $em->source = 'tickmas';
                $date_t_format = $e->dates->start->dateTime ?? 'null';
                $start_date_time = \DateTime::createFromFormat(\DateTime::ISO8601, $date_t_format);
                $em->start_datetime_utc = $start_date_time->format('Y-m-d H:i:s');//2020-03-01T03:00:00Z
                if ($e->name) {
                    $em->name = $e->name;
                } else continue;
                if (! property_exists($e, 'jsonLd')) {
                    echo 'item miss jsonLd';
                    continue;
                }
                $json = $e->jsonLd;
                $em->short_desc = ($json->description) ?? '';
                $em->description = $em->short_desc;
                $images_filtered = $e->imagesFiltered;
                if (! empty($images_filtered)) {
                    $large_imgs = array_filter($images_filtered, function ($i) {
                        return strpos($i->url, 'LARGE') !== false;
                    });
                    $em->img = (array_pop($large_imgs))->url ?? null;
                }

                $em->min_cost = $e->priceRanges->min ?? null;
                $em->website = $e->eventUrlLink ?? null;
                $em->genre = $e->genreName;
                $em->system_note = json_encode(['id' => $e->id]);
                try {
                    $em->save();
                } catch (\Exception $exception) {
                    Yii::error($exception);
                    continue;
                }
                if ($em->errors) {
                    Yii::error($em->errors);
                    continue;
                }
                if (! property_exists($json, 'location')) {
                    echo 'json no location';
                    continue;
                }
                $l = $json->location;
                $v = Venue::findOrCreate(['name' => $l->name]);
                /** @var $v Venue */
                $v->source = 'tickmas';
                $v->website = $l->sameAs ?? null;
                if (property_exists($l, 'address')) {
                    $v->address1 = $l->address->streetAddress ?? null;
                    $v->city = $l->address->addressLocality ?? null;
                    $v->state = $l->address->addressRegion ?? null;
                    $v->zip = $l->address->postalCode ?? null;
                    $first_venue = array_pop($e->venues) ?? null;
                    if (is_object($first_venue) && $first_venue) {
                        $v->attr = ['id' => $first_venue->id, 'seoLink' => $first_venue->seoLink];
                    }
                }
                try {
                    $v->save();
                } catch (\Exception $exception) {
                    Yii::error($exception);
                    continue;
                }
                if ($v->errors) {
                    Yii::error($v->errors);
                    continue;
                }
                $em->venue_id = $v->id;
                //now parse bands
                if (! property_exists($json, 'performer')) {
                    echo 'json no performer';
                    continue;
                }
                foreach ($json->performer as $b) {
                    $bm = Band::findOrCreate(['name' => $b->name]);
                    /** @var Band $bm */
                    $bm->source = 'tickmas';
                    $bm->website = $b->sameAs;
                    $bm->logo = $em->img;
                    try {
                        $bm->save();
                    } catch (\Exception $e) {
                        Yii::error($e);
                        continue;
                    }
                    if ($bm->errors) {
                        Yii::error($bm->errors);
                        continue;
                    }
                    $band_event = new BandEvent();
                    $band_event->band_id = $bm->id;
                    $band_event->event_id = $em->id;
                    $band_event->created_by = $this->SCRAPER_ID;

                    try {
                        $band_event->save();
                    } catch (\Exception $exception) {
                        //ignore. probably duplicate band_event
                    }
                    if ($band_event->errors) {
                        Yii::error($band_event->errors);
                    }
                }
                $scraped++;
                if ($scraped > MAX_SCRAPE_PER_DAY) $scrape_next_page = false;
            }
        } while ($scrape_next_page);
        echo "Pulled: $scraped events";
        return true;
    }

    /**
     * Pull band details for reverbnation
     * Getting them from https://www.reverbnation.com/api/artist/3981578
     */
    public function actionPullBandReverb()
    {
        $K_LIMIT = 1000;
//        $bands = Band::findAll(['source' => 'reverb', 'description' => null]);
        $bands = Band::findBySql("SELECT  id, attr, website FROM `band` WHERE `scrape_status`='init' AND `source`='reverb' 
            AND COALESCE(`logo`,'')='' LIMIT :limit ", [':limit' => $K_LIMIT])->all();
        $goutte = new Client();
        $guzzle = new \GuzzleHttp\Client();
        $base_api_url = 'https://www.reverbnation.com/api/artist/';
        $scraped = 0;
        foreach ($bands as $band) {
            $attr = $band->attr;
            $url = ($attr['url'] ?? $band->website);
            if (empty($url)) {
                $band->scrape_status = 'url_miss';
                $band->saveAndLogError();
                continue;
            }
            $crawler = $goutte->request('get', $url);
            $image_src = $crawler->filter('meta[name="image_src"]');
            try {
                $image_src = $image_src->attr('content');
                $band_id = null;
                if (preg_match("/.*\/artists\/images\/(\d+).*/", $image_src, $band_id) && isset($band_id[1])) {
                    $band_id = $band_id[1];////https://gp1.wac.edgecastcdn.net/802892/http_public_production/artists/images/3981578/original/crop:x0y0w756h567/hash:1467524721/1461597472_E0694166-AE0B-43F0-8ED0-966218024D8C-1314-0000015D04CE333B.jpg?1467524721;
                }
            } catch (\Exception $exception) {
                continue;
            }
            if (! is_int(intval($band_id)) || ! is_string($band_id)) {
                continue;
            }
            $band_api_data = $guzzle->request('get', $base_api_url . $band_id);
            if ($band_api_data->getStatusCode() !== 200) {
                echo 'Failed. ' . $band_api_data->getStatusCode();
                continue;
            }
            $band_api_data = $band_api_data->getBody();
            if (! method_exists($band_api_data, 'getContents')) {
                echo 'Bad response. Url: ' . $url;
                continue;
            }
            $band_api_data = $band_api_data->getContents();
            try {
                $band_api_data = json_decode($band_api_data);
            } catch (\Exception $exception) {
                continue;
            }
            $band->attr = $band_api_data;
            $band->description = $band_api_data->bio;
            if (property_exists($band_api_data, 'cover_photo')) {
                $band->logo = $band_api_data->cover_photo->url;
            }
            if (empty($band->logo)) {
                $band->logo = property_exists($band_api_data, 'image') ? ('https:' . $band_api_data->image) : null;
            }
            $band->lno_score = random_int(6, 10);
            $band->genre = implode(',', $band_api_data->genres);
            $band->facebook = $band_api_data->fb_share_url ? $band_api_data->fb_share_url : null;
            try {
                $band->save();
            } catch (\Exception $exception) {
                continue;
            }
            if ($band->errors) {
                Yii::error($band->errors);
            } else {
                $scraped++;
            }
        }
        echo "Scraped $scraped bands" . PHP_EOL;
    }

    /**
     * Pull from https://www.songkick.com/metro_areas/11086-us-san-diego
     */
    public function actionPullSongkick()
    {
        $bands = Band::findAll(['source' => 'reverb', 'description' => null]);
        $goutte = new Client();
        $guzzle = new \GuzzleHttp\Client();
        $base_api_url = 'https://www.songkick.com/metro_areas/';
        $metro = '11086-us-san-diego';

        //        $IS_DEBUG = true;
        $IS_DEBUG = false;
        $scraped = 0;
        $url = "";
        if ($IS_DEBUG) {
            $events = file_get_contents(dirname(__DIR__) . "/web/scrape/reverb_ev.json");
//            var_dump($events);
//            return true;
        } else {
            $params = [];
            $guzzle = new GuzzleClient();
            $events = $guzzle->request('GET', $url, $params);
            if ($events->getStatusCode() !== 200) {
                echo 'Failed. ' . $events->getStatusCode();
                return false;
            }
            $events = $events->getBody();
            if (! method_exists($events, 'getContents')) {
                echo 'Bad response. Url: ' . $url;
                return false;
            }
            $events = $events->getContents();
        }
        $events = json_decode($events);

        echo "Scraped $scraped bands" . PHP_EOL;
        return true;
    }
}
