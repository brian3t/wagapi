-- for MYSQL, after deploying yii2 usario user

-- run migrations: m201114_053627_add_usr_ava_column_to_profile_table_201113.php

alter table user   add balance int default 0 null;
-- added table wager, invi

