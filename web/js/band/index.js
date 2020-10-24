//let's get the api url
let apiurl = window.location.href.replace('://', '://api.').replace('/index', '')
let url_parts = apiurl.split('/')
let endpoint = url_parts[url_parts.length - 1]
apiurl = apiurl.replace(endpoint, 'v1/' + endpoint)
let search_portion = (new URL(apiurl)).search
apiurl = apiurl.replace(search_portion, '')
apiurl = apiurl.replace('admin.', '')
$('body').on('click', function (e){
    $('[data-toggle="popover-x"]').each(function (){
        if (! $(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
            let $this = $(this), href = $this.attr('href'),
                $dialog = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))), //strip for ie7
                option = $dialog.data('popover-x') ? 'toggle' : $.extend({remote: ! /#/.test(href) && href});
            if ($dialog.popoverX) $dialog.popoverX('hide').on('hide');
        }
    });
});

function popover_video(video_id){
}

$('#yt_vid_popover').on('show.bs.modal', function (event){
    let button = $(event.relatedTarget) // Button that triggered the modal
    let vidid = button.data('vidid') // Extract info from data-* attributes
    let bandid = button.data('bandid')
    let modal = $(this)
    modal.data('vidid', vidid)
    modal.data('bandid', bandid)
    $(modal.find('#ytlink_first')).attr('src', `https://youtube.com/embed/${vidid}?&autoplay=1`)
})
$('#yt_vid_popover button.vid_approve').on('click', function (event){
    console.log(`event: `, event)
    let is_approve = $(event.target).data('is_approve')
    let $modal = $($(event.target).closest('.modal')) //button
    let bandid = $modal.data('bandid')
    $.ajax(apiurl + `/${bandid}`, {
        contentType: 'application/json', crossOrigin: true, data: JSON.stringify({ytlink_approved: is_approve}), dataType: 'json', method: 'PATCH', success: function (res){
            if (! res) {
                $.notify({
                    message: 'API update failed. Try again'
                }, {
                    type: 'danger', placement: {align: 'center'}
                });
            }
            if (res.ytlink_approved !== is_approve) {
                $.notify({
                    message: 'API update called. But value not 1'
                }, {
                    type: 'danger', placement: {align: 'center'}
                })
            }
            if (res.ytlink_approved === is_approve) {
                $.notify({
                    message: 'API update successful'
                }, {
                    type: 'success', placement: {align: 'center'}
                });
                $.pjax.reload({container: '#kv-pjax-container-band'})
            }
        }
    })
})
