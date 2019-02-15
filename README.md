# db.class
Simple PHP class for MySQL, allowing to call SUID without any mappings (like $db->users->get(2)) for table "users"

# SHOWCASE:

```php
var_dump($d->test_table_users()->values(array("name"=>"kirill","email"=>"kir@mail.by", "password"=>"321"))->insert());

var_dump($d->test_table_users()->get(1));
var_dump($d->test_table_users()->get(array("password"=>"321")));
var_dump($d->test_table_users()->email()->get(1));
var_dump($d->test_table_users()->columns('email, id, password')->get(1));
var_dump($d->test_table_users()->get(array("email"=>"asd", "password"=>"321")));
var_dump($d->test_table_users()->get(array("email"=>"asd", "password"=>"321"), "OR"));
var_dump($d->test_table_users()->get());
var_dump($d->test_table_users()->email("kirill@mail.by")->set(1));
var_dump($d->test_table_users()->values(array("email"=>"kir@mail.by", "password"=>"123"))->set(1));
var_dump($d->test_table_users()->get(1));
var_dump($d->test_table_users()->values(array("email"=>"kir@mail.by", "password"=>"123"))->set(array("email"=>"123@asd.com")));
var_dump($d->test_table_users()->delete(1));
var_dump($d->test_table_users()->delete(array("email"=>"123.@com.by")));
```
# DB Example
```
CREATE TABLE `test_table_users` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `test_table_users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `test_table_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
  
INSERT INTO `test_table_users` (`id`, `name`, `email`, `password`) VALUES
(2, 'kirill', 'mail@mail.by', '321');
```
