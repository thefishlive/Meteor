DELETE FROM `{table.tokens}`
WHERE `token` = '{0}' AND `client-id` = '{1]'
LIMIT 1;