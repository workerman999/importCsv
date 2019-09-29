<?php
include 'classes/CsvImporter.php';

$importCategory = new CsvImporter("files/groups.csv", true,';');
$importProducts = new CsvImporter("files/products.csv", true,';');

$isCategory = false;
$isProducts = false;

$dataCategory = $importCategory->get();
$dataProducts = $importProducts->get();

if ($dataCategory) {
    $isCategory = true;
}
if ($dataProducts) {
    $isProducts = true;
}

if ($dataCategory && $dataProducts) {
    $catalog = [];

    foreach ($dataProducts as $product) {
        if (isset($dataCategory[$product['категория'] - 1])){
            $dataCategory[$product['категория'] - 1]['products'][] = $product;
        }
    }

    foreach ($dataCategory as $category) {
        if ($category['родитель']) {
            $catalog = search_parent_category($category['родитель'], $catalog, $category);
        } else {
            $catalog[$category['id']] = $category;
            $catalog[$category['id']]['level'] = 1;
        }
    }
}

/**
 * Распределение товаров по категориям
 * @param string $searchKey Ключ который ищем
 * @param array $arr Массив в котором ищем
 * @param array $result Массив который добавляем
 */
function search_parent_category($searchKey, array $arr, array $item)
{
    if (isset($arr[$searchKey])) {
        $arr[$searchKey][$item['id']] = $item;
        $arr[$searchKey][$item['id']]['level'] = $arr[$searchKey]['level'] + 1;
        if ($arr[$searchKey]['наследовать дочерним'] && !$arr[$searchKey][$item['id']]['наследовать дочерним']) {
            $arr[$searchKey][$item['id']]['формат описания товаров'] = $arr[$searchKey]['формат описания товаров'];
        }
        return $arr;
    }
    foreach ($arr as $key => $param) {
        if (is_array($param) && ($key != 'products')) {
            $arr[$key] = search_parent_category($searchKey, $param, $item);
        }
    }
    return $arr;
}

/**
 * Рекурсия по списку продуктов
 * @param $products
 * @param $offset
 * @param $description
 * @return string
 */
function getProducts($products, $offset, $description)
{
    $offset .= "\t";
    foreach ($products as $product) {
        $tree = $offset;
        preg_match_all ('/%[^\s]*[^\s]*%/ui', $description, $matches);
        foreach ($matches[0] as $match) {
            $key = str_ireplace('%', '', $match);
            if (isset($product[$key])) {
                $description = str_ireplace($match, $product[$key], $description);
            } else {
                $description = str_ireplace($match, 'UNDEFINED', $description);
            }
        }
        $tree .= "<li><b>" . $description . "</b></li>\n";
    }
    return $tree;
}

/**
 * Рекурсия по списку категорий
 * @param $categories
 * @param $offset
 * @return string
 */
function getCategory($categories, $offset)
{
    $offset .= "\t";
    $startOffset = $offset;
    $tree = $offset . "<li>\n";
    $offset .= "\t";
    $tree .= $offset . "<h" . $categories['level'] . '>' . $categories['наименование'] . '</h' . $categories['level'] . ">\n";
    $tree .= $offset . "<ul>\n";
    foreach ($categories as $key => $item) {
        if ($key == 'products') {
            $tree .= getProducts($item, $offset, $categories['формат описания товаров']);
        }
        if (is_array($item) && ($key != 'products')) {
            $tree .= getCategory($item, $offset);
        }
    }
    $tree .= $offset . "</ul>\n";
    $tree .= $startOffset . "</li>\n";
    return $tree;
}

/**
 * Построение дерева
 * @param array $cats
 * @return string
 */
function build_tree(Array $cats)
{
    $startOffset = $offset = "\t\t\t\t";
    $offset .= "\t";
    $tree = "<ul>\n";
    foreach($cats as $key => $cat){
        $tree .= $startOffset . "<li>\n";
        $tree .= $offset . "<h" . $cat['level'] . '>' . $cat['наименование'] . '</h' . $cat['level'] . ">\n";
        $tree .= $offset . "<ul>\n";
        foreach ($cat as $catKey => $item) {
            if ($catKey == 'products') {
                $tree .= getProducts($item, $offset, $cat['формат описания товаров']);
            }
            if (is_array($item) && ($catKey != 'products')) {
                $tree .= getCategory($item, $offset);
            }
        }
        $tree .= $offset . "</ul>\n";
        $tree .= $startOffset . "</li>\n";
    }
    $tree .= "\t\t\t" . "</ul>\n";
    return $tree;
}

require_once('view/index.php');