<?php
require_once 'LE.php';

$db = new LE ('01_persons');

//$db->create_db('abcdef');

//$db->delete_db('dbkk');
// =========================

$sql =<<<sql
    create table orders (
    id int not null auto_increment,
    name char(20),
    family char(20),
    primary key (id)
   )
sql;
//$db->query ($sql);

// =========================

//$data = [
//    'f_name' => 'flor',
//    'l_name' => 'sarjukhe',
//    'city' => 'landan',
//];
//echo'<pre><b>';
//print_r($db->set_value_parameter($data, $db::SVP_SIDE_BY_SIDE));
//echo'</b></pre>';

//echo'<pre><b>';
//print_r($_SERVER);
//echo'</b></pre>';

// =========================

//$db->show_tables($db::OP_PRINT_R);
//$db->show_databases($db::OP_PRINT_R);

// =========================

//echo'<pre><b>';
//print_r($db->row_count('persons', 'id', ['aa' => 'aa',]));
//echo'</b></pre>';

// =========================

//echo'<pre><b>';
//var_dump($db->value_exists('persons', ['first_name' => 'shir']));
//echo'</b></pre>';

// =========================
$ar = [
    'a' => ['first_name' => 'uzi',
    'last_name' => '<a href="#">link 1</a>',],
    
    'b' => ['first_name' => 'colt',
    'last_name' => '<a href="#">link 2</a>',],
];

$ar2 = [
    'first_name' => 'sarbazi',
    'last_name' => '2 year',
];

$ar3 = [
    'a1' => '<a href="#">link 1</a>',
    'a2' => '<a href="#">link 2</a>',
];

//$db->add_row('persons', $ar);

// =========================

//echo'<pre><b>';
//print_r($db->get_row('persons'));
//echo'</b></pre>';

//echo'<pre><b>';
//var_dump ($db->row_count ('persons', ['first_name' => 'uzi',]));
//echo'</b></pre>';

// =========================
$o = [
    'name' => 'cc',
    'family' => 'cccc',
];
//$db->add_row ('orders', $o);
//$db->empty_tbl ('orders');
// =========================
$a = [
    'name' => 'aa',
    'or',
    'name' => 'cc',
];

//$db->get_row ('orders', '*', $a);
//$db->value_exists ('orders', ['a' => '1',]);

// =========================

//$db->select ('name,family');
//$db->from ('orders');

//$wop = ['>', '!='];

$cop = [$db::CO_OR, $db::CO_OR];

//$db->where ($w, [], $cop);

//$db->order_by ('family');

//$db->limit (2);

$w = [
    'name' => 'x',
//    'email' => 'p@email.ir',
];

// =========================

//$db->row_count ('orders',$db::OP_HTML_TABLE);

// =========================

// ----------------------------------------
// method chaining

//$db->select ('*')
//->from ('users')
//->min ('score')
//->max ('time')
//->avg ('score')
//->sum ('score')
//->count ('score')
//->where ($w)
//->order_by ('id', true)
//->limit (5,109)
//->group_by ('family')
//->in ('score', [60,46,41])
//->is_null ('name')
//->between ('name','a', 'z')
//->run ($db::OP_HTML_TABLE, true);

// =========================
$del = [
    'name' => 'y',
    'email' => 'q',
];
//$db->delete ('users')
//    ->where ($del)
//    ->run ($db::OP_HTML_TABLE, true);

// ----------------------------------------
$updateWherer = [
    'hasan' => 'name',
    'hasan@EMAIL.IR' => 'email',
];

$newData = [
    'name' => 'zelatan',
    'email' => 'zelatan@email.ir',
];

//$db->update ('users', $newData)
//        ->where ($updateWherer)
//        ->run ($db::OP_HTML_TABLE, true);

// ----------------------------------------

$ad = [
//    'name' => 's,u,2',
    'name' => 's,u,1',
    'email' => 'e,u,1',
    'score' => 'r,0-100',
//    'score' => 'r,1-100',
    'time' => 't',
    'active' => 'f,1',
];
//$db->empty_tbl ('users');
//$db->ard ('users', $ad, 200);

//$insertInto = ['one line', 'oneline@email.ir',1111,'20/20/20',1];
$insertInto = [
    'email' => 'GG',
    'time' => '20/20/20',
    'active' => 1,
];
//$db->add_row ('users', $insertInto);
//$db->query ("select min(score) as aa from users where score > 95 having min(aa) > 95",$db::OP_HTML_TABLE);
// =========================

//echo'<pre><b>';
//print_r ($db->query ("select * from orders where name is not null"));
//echo'</b></pre>';


// =========================

//$db->exists ('users', 'gg');
//var_dump ($db->value_exists ('users', ['email' => 'ggg']));
// =========================

$any = [
    'name' => 'bb',
];

//$subQuery = $db->select ('family')
//->from ('orders')
//->where (['name' => 'aa'])
//->run ($db::OP_QUERY);

//$db->select ('*')
//->from ('users')
//->where (['name' => 'j' , 'email' => 's@email.ir'])
//->all ('name',$subQuery, '<>')
//->any ('name', $subQuery)
//->run ($db::OP_HTML_TABLE, true);

// =========================
$data = [
    'id' => 'int not null auto_increment',
//    'name' => 'char(10)',
//    'email' => 'char(30) not null default "EMAIL"',
    'age' => 'tinyint(3)',
    'city' => "enum('hamedan' , 'tehran' , 'arak')",
//    'u_id' => 'int',
    'p_id' => 'int',
];

//$db->create_table ('persons20', $data, 'id')
//->check ('age > 20')
//->foreign_key (['p_id' => 'persons2(id)'])
//->run ($db::RUN_CREATE_TABLE, $db::OP_NO_CHANGE, true);

//$db->aio ('persons20', 999);


$ii = [
    'age' => '22',
    'city' => 'tehran',
    'p_id' => 2,
];
//$db->add_row ('persons20', $ii);

// =========================

//$db->drop_default ('persons9', 'name')
//->set_default ('persons9', 'name', 'NO NAME');

//$db->drop_foreign_key ('persons7', 'p_id');

// =========================

$ct = [
    'id' => 'int not null auto_increment',
    'name' => 'char(10)',
    'p_id' => 'int',
];

//$db->create_table ('persons21', $ct, 'id')
//->run ($db::RUN_CREATE_TABLE);

//$db->add_foreign_key ('persons21', 'p_id', 'persons2', 'id');

//$db->drop_primary_key ('persons2');
// =========================

//$db->select ()
//    ->from ('persons2')
//    ->if_null ('name', '---')
//    ->run ($db::RUN_SQL, $db::OP_HTML_TABLE, true);

// =========================
$condRes = [
    'score > 0 and score <= 10' => 'D',
    'score >= 11 and score <= 20' => 'C',
    'score >= 21 and score <= 30' => 'B',
    'score >= 31 and score <= 40' => 'A',
];
//$db->select ('name,score')
//->from ('users')
//->case ($condRes, 'Ranking', 'NO RANK')
//->run ($db::RUN_SQL, $db::OP_HTML_TABLE, true);

// =========================

//$db->select ('*')
//->from ('persons2')
//->run ($db::OP_HTML_TABLE);

// =========================

$category = [
    'id' => 'int not null unique auto_increment',
    'name' => 'char(10) not null unique',
];

$product = [
    'id' => 'int not null unique auto_increment',
    'name' => 'char(10)',
    'price' => 'char(40)',
    'c_id' => 'int',
];

//$db->create_table ('category', $category, 'id')
//    ->run ($db::RUN_CREATE_TABLE);

//$db->create_table ('product', $product, 'id')
//    ->foreign_key (['c_id' => 'category(id)'])
//    ->run ($db::RUN_CREATE_TABLE);

$catData = [
    'name' => 's,l,2',
];

//$db->ard ('category', $catData, 20);

$prodData = [
    'name' => 's,l,3',
    'price' => 'r,1000-5000',
    'c_id' => 'r,1-20',
];

//$db->ard ('product', $prodData, 20);

$wj = [
//    'category.id' => '5',
    'c.name' => 'bu',
];

//$db->select ('c.name as cn, p.name as pn', TRUE)
//->from ('category as c')
//->join ('product as p', '', $db::CROSS_JOIN)
//->order_by ('c.id')
//->where ($wj)
//->join ('users as u', 'p.id=u.id', $db::LEFT_JOIN)
//->run ($db::RUN_SQL, $db::OP_HTML_TABLE, true);

// =========================

$union[] = $db->select ("c.name")
    ->from ('category as c')
    ->run ($db::RUN_SQL, $db::OP_QUERY);

$union[] = $db->select ("p.name")
    ->from ('product as p')
    ->run ($db::RUN_SQL, $db::OP_QUERY);

//$db->union ($union, $db::OP_HTML_TABLE);

// =========================

$ard = [
    'name' => 'e,l,2-5',
    'price' => 'f,1000',
];
//$db->empty_tbl ('product');
//$db->ard ('product', $ard, 100);

// =========================

$db = null;

// =========================

// taligh shodeh ha
// mysql functions for example count(), min(), max()