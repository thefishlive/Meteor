SELECT count(`token`) AS `count`
FROM `{table.tokens}`
WHERE `token` = '{0}' AND `client-id` = '{1}' AND `user` = '{2}' AND `expires` > NOW();