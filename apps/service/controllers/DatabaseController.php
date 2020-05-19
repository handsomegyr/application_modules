<?php

namespace App\Service\Controllers;

use \Phalcon\Db\Column;
use \Phalcon\Db\Index;
use \Phalcon\Db\Reference;

class DatabaseController extends ControllerBase
{

    private $connectionFrom;

    private $connectionTo;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();

        set_time_limit(0);
        $di = $this->getDI();

        // $this->connectionFrom = $di['dbfrom'];
        // $this->connectionFrom->execute("SET NAMES 'utf8mb4';");

        $this->connectionTo = $di['db'];
        $this->connectionTo->execute("SET NAMES 'utf8mb4';");
    }

    /**
     * 1.从shopnc的type表将数据导入到igoods_type表中
     */
    public function transfertypeAction()
    {
        // http://phalconm4local/service/database/transfertype
        // http://phalconm/service/database/transfertype
        // 从源表中获取数据
        $this->connectionTo->execute("Delete FROM igoods_type");
        $result = $this->connectionFrom->query('SELECT * FROM type order by type_id asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        $datetime = date('Y-m-d H:i:s');
        while ($item = $result->fetch()) {
            // print_r($item);
            // die('xxx');
            $_id = myMongoId(new \MongoId());
            $success = $this->connectionTo->execute("INSERT INTO igoods_type(_id,name,sort,category_id,category_name,__CREATE_TIME__,__MODIFY_TIME__,__REMOVED__,shopnc_type_id,shopnc_class_id) VALUES (?,?,?,?,?,?,?,?,?,?)", array(
                $_id, // _id
                $item->type_name, // name
                $item->type_sort, // sort
                '', // category_id
                $item->class_name, // category_name
                $datetime, // __CREATE_TIME__
                $datetime, // __MODIFY_TIME__
                0, // __REMOVED__
                $item->type_id, // shopnc_type_id
                $item->class_id // shopnc_class_id
            ));
        }
        echo "OK<br/>";

        return;
    }

    /**
     * 2.
     * 从shopnc的goods_class表中将数据导入到igoods_category
     */
    public function transfercategoryAction()
    {
        // http://phalconm4local/service/database/transfercategory
        // http://phalconm/service/database/transfercategory

        // 从源表中获取数据
        // $statement = $this->connectionFrom->prepare('SELECT * FROM goods_class Where 1=:name');
        $this->connectionTo->execute("Delete FROM igoods_category");
        $result = $this->connectionFrom->query('SELECT * FROM goods_class order by gc_id asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        $datetime = date('Y-m-d H:i:s');

        while ($item = $result->fetch()) {
            $_id = myMongoId(new \MongoId());
            $parent_id = '';
            $type_id = '';
            if (!empty($item->type_id)) {
                $typeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_type where shopnc_type_id={$item->type_id}", \Phalcon\Db::FETCH_ASSOC);
                // print_r($typeInfo);
                $type_id = $typeInfo['_id'];
                // die('xxx' . $type_id);
            }
            $success = $this->connectionTo->execute("INSERT INTO igoods_category(_id,name,type_id,type_name,parent_id,commis_rate,sort,virtual,title,keywords,description,__CREATE_TIME__,__MODIFY_TIME__,__REMOVED__,shopnc_class_id,shopnc_class_parent_id,shopnc_type_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array(
                $_id, // _id
                $item->gc_name, // name
                $type_id, // type_id
                $item->type_name, // type_name
                $parent_id, // parent_id
                $item->commis_rate, // commis_rate
                $item->gc_sort, // sort
                $item->gc_virtual, // virtual
                $item->gc_title, // title
                $item->gc_keywords, // keywords
                $item->gc_description, // description
                $datetime, // __CREATE_TIME__
                $datetime, // __MODIFY_TIME__
                0, // __REMOVED__
                $item->gc_id, // shopnc_class_id
                $item->gc_parent_id, // shopnc_class_parent_id,
                $item->type_id // shopnc_type_id
            ));
        }

        // 更新parent_id
        $result = $this->connectionTo->query('SELECT * FROM igoods_category order by _id asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        while ($item = $result->fetch()) {
            $parent_id = '';
            if (!empty($item->shopnc_class_parent_id)) {
                $parentInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_category where shopnc_class_id={$item->shopnc_class_parent_id}", \Phalcon\Db::FETCH_ASSOC);
                $parent_id = $parentInfo['_id'];
            }
            $this->connectionTo->execute("UPDATE igoods_category SET parent_id='{$parent_id}' WHERE _id='{$item->_id}' ");
        }
        echo "OK<br/>";

        return;
    }

    /**
     * 3.更新商品类型的分类字段
     */
    public function updatetypeAction()
    {
        // http://phalconm4local/service/database/updatetype
        // http://phalconm/service/database/updatetype
        // 从源表中获取数据
        $result = $this->connectionTo->query('SELECT * FROM igoods_type order by _id asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        $datetime = date('Y-m-d H:i:s');
        while ($item = $result->fetch()) {
            $category_id = '';
            if (!empty($item->shopnc_class_id)) {
                $categoryInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_category where shopnc_class_id={$item->shopnc_class_id}", \Phalcon\Db::FETCH_ASSOC);
                $category_id = $categoryInfo['_id'];
            }
            $this->connectionTo->execute("UPDATE igoods_type SET category_id='{$category_id}' WHERE _id='{$item->_id}' ");
        }
        echo "OK<br/>";

        return;
    }

    /**
     * 4.
     * 从shopnc的brand表将数据导入到igoods_brand表中
     */
    public function transferbrandAction()
    {
        // http://phalconm4local/service/database/transferbrand
        // http://phalconm/service/database/transferbrand

        // 从源表中获取数据
        $this->connectionTo->execute("Delete FROM igoods_brand");
        $result = $this->connectionFrom->query('SELECT * FROM brand order by brand_id asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        $datetime = date('Y-m-d H:i:s');

        while ($item = $result->fetch()) {
            $_id = myMongoId(new \MongoId());
            $store_id = '';
            $category_id = '';
            if (!empty($item->class_id)) {
                $categoryInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_category where shopnc_class_id={$item->class_id}", \Phalcon\Db::FETCH_ASSOC);
                $category_id = $categoryInfo['_id'];
            }
            $success = $this->connectionTo->execute("INSERT INTO igoods_brand(_id,name,initial,category_name,pic,sort,recommend,store_id,apply,category_id,show_type,__CREATE_TIME__,__MODIFY_TIME__,__REMOVED__,shopnc_brand_id,shopnc_class_id,shopnc_store_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array(
                $_id, // _id
                $item->brand_name, // name
                $item->brand_initial, // initial
                $item->brand_class, // category_name
                'brand/' . $item->brand_pic, // pic
                $item->brand_sort, // sort
                $item->brand_recommend, // recommend
                $store_id, // store_id
                $item->brand_apply, // apply
                $category_id, // category_id
                $item->show_type, // show_type
                $datetime, // __CREATE_TIME__
                $datetime, // __MODIFY_TIME__
                0, // __REMOVED__
                $item->brand_id, // shopnc_brand_id
                $item->class_id, // shopnc_class_id
                $item->store_id // shopnc_store_id
            ));
        }
        echo "OK<br/>";

        return;
    }

    /**
     * 5.
     * 从shopnc的spec表将数据导入到igoods_spec表中
     */
    public function transferspecAction()
    {
        // http://phalconm4local/service/database/transferspec
        // http://phalconm/service/database/transferspec

        // 从源表中获取数据
        $this->connectionTo->execute("Delete FROM igoods_spec");
        $result = $this->connectionFrom->query('SELECT * FROM spec order by sp_id asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        $datetime = date('Y-m-d H:i:s');

        while ($item = $result->fetch()) {
            $_id = myMongoId(new \MongoId());
            $category_id = '';
            if (!empty($item->class_id)) {
                $categoryInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_category where shopnc_class_id={$item->class_id}", \Phalcon\Db::FETCH_ASSOC);
                $category_id = $categoryInfo['_id'];
            }
            $success = $this->connectionTo->execute("INSERT INTO igoods_spec(_id,name,sort,category_id,category_name,__CREATE_TIME__,__MODIFY_TIME__,__REMOVED__,shopnc_sp_id,shopnc_class_id) VALUES (?,?,?,?,?,?,?,?,?,?)", array(
                $_id, // _id
                $item->sp_name, // name
                $item->sp_sort, // sort
                $category_id, // category_id
                $item->class_name, // category_name
                $datetime, // __CREATE_TIME__
                $datetime, // __MODIFY_TIME__
                0, // __REMOVED__
                $item->sp_id, // shopnc_sp_id
                $item->class_id // shopnc_class_id
            ));
        }
        echo "OK<br/>";

        return;
    }

    /**
     * 6.
     * 从shopnc的spec_value表将数据导入到igoods_spec_value表中
     */
    public function transferspecvalueAction()
    {
        // http://phalconm4local/service/database/transferspecvalue
        // http://phalconm/service/database/transferspecvalue

        // 从源表中获取数据
        $this->connectionTo->execute("Delete FROM igoods_spec_value");
        $result = $this->connectionFrom->query('SELECT * FROM spec_value order by sp_value_id asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        $datetime = date('Y-m-d H:i:s');

        while ($item = $result->fetch()) {
            $_id = myMongoId(new \MongoId());
            $store_id = '';

            $sp_id = '';
            if (!empty($item->sp_id)) {
                $specInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_spec where shopnc_sp_id={$item->sp_id}", \Phalcon\Db::FETCH_ASSOC);
                $sp_id = $specInfo['_id'];
            }

            $gc_id = '';
            if (!empty($item->gc_id)) {
                $categoryInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_category where shopnc_class_id={$item->gc_id}", \Phalcon\Db::FETCH_ASSOC);
                $gc_id = $categoryInfo['_id'];
            }

            $success = $this->connectionTo->execute("INSERT INTO igoods_spec_value(_id,name,sp_id,gc_id,store_id,color,sort,__CREATE_TIME__,__MODIFY_TIME__,__REMOVED__,shopnc_sp_value_id,shopnc_sp_id,shopnc_gc_id,shopnc_store_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array(
                $_id, // _id
                $item->sp_value_name, // name
                $sp_id, // sp_id
                $gc_id, // gc_id
                $store_id, // store_id
                $item->sp_value_color, // color
                $item->sp_value_sort, // sort
                $datetime, // __CREATE_TIME__
                $datetime, // __MODIFY_TIME__
                0, // __REMOVED__
                $item->sp_value_id, // shopnc_sp_value_id
                $item->sp_id, // shopnc_sp_id
                $item->gc_id, // shopnc_gc_id
                $item->store_id // shopnc_store_id
            ));
        }
        echo "OK<br/>";

        return;
    }

    /**
     * 7.
     * 从shopnc的type_brand表将数据导入到igoods_type_brand表中
     */
    public function transfertypebrandAction()
    {
        // http://phalconm4local/service/database/transfertypebrand
        // http://phalconm/service/database/transfertypebrand

        // 从源表中获取数据
        $this->connectionTo->execute("Delete FROM igoods_type_brand");
        $result = $this->connectionFrom->query('SELECT * FROM type_brand order by type_id asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        $datetime = date('Y-m-d H:i:s');

        while ($item = $result->fetch()) {
            $_id = myMongoId(new \MongoId());
            $store_id = '';

            $type_id = '';
            if (!empty($item->type_id)) {
                $typeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_type where shopnc_type_id={$item->type_id}", \Phalcon\Db::FETCH_ASSOC);
                $type_id = $typeInfo['_id'];
            }

            $brand_id = '';
            if (!empty($item->brand_id)) {
                $brandInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_brand where shopnc_brand_id={$item->brand_id}", \Phalcon\Db::FETCH_ASSOC);
                $brand_id = $brandInfo['_id'];
            }

            $success = $this->connectionTo->execute("INSERT INTO igoods_type_brand(_id,type_id,brand_id,__CREATE_TIME__,__MODIFY_TIME__,__REMOVED__,shopnc_type_id,shopnc_brand_id) VALUES (?,?,?,?,?,?,?,?)", array(
                $_id, // _id
                $type_id, // type_id
                $brand_id, // brand_id
                $datetime, // __CREATE_TIME__
                $datetime, // __MODIFY_TIME__
                0, // __REMOVED__
                $item->type_id, // shopnc_type_id
                $item->brand_id // shopnc_brand_id
            ));
        }
        echo "OK<br/>";

        return;
    }

    /**
     * 8.
     * 从shopnc的type_spec表将数据导入到igoods_type_spec表中
     */
    public function transfertypespecAction()
    {
        // http://phalconm4local/service/database/transfertypespec
        // http://phalconm/service/database/transfertypespec

        // 从源表中获取数据
        $this->connectionTo->execute("Delete FROM igoods_type_spec");
        $result = $this->connectionFrom->query('SELECT * FROM type_spec order by type_id asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        $datetime = date('Y-m-d H:i:s');

        while ($item = $result->fetch()) {
            $_id = myMongoId(new \MongoId());
            $store_id = '';

            $type_id = '';
            if (!empty($item->type_id)) {
                $typeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_type where shopnc_type_id={$item->type_id}", \Phalcon\Db::FETCH_ASSOC);
                $type_id = $typeInfo['_id'];
            }

            $sp_id = '';
            if (!empty($item->sp_id)) {
                $specInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_spec where shopnc_sp_id={$item->sp_id}", \Phalcon\Db::FETCH_ASSOC);
                $sp_id = $specInfo['_id'];
            }

            $success = $this->connectionTo->execute("INSERT INTO igoods_type_spec(_id,type_id,sp_id,__CREATE_TIME__,__MODIFY_TIME__,__REMOVED__,shopnc_type_id,shopnc_sp_id) VALUES (?,?,?,?,?,?,?,?)", array(
                $_id, // _id
                $type_id, // type_id
                $sp_id, // sp_id
                $datetime, // __CREATE_TIME__
                $datetime, // __MODIFY_TIME__
                0, // __REMOVED__
                $item->type_id, // shopnc_type_id
                $item->sp_id // shopnc_sp_id
            ));
        }
        echo "OK<br/>";

        return;
    }

    /**
     * 9.
     * 从shopnc的attribute表将数据导入到igoods_attribute表中
     */
    public function transferattributeAction()
    {
        // http://phalconm4local/service/database/transferattribute
        // http://phalconm/service/database/transferattribute

        // 从源表中获取数据
        $this->connectionTo->execute("Delete FROM igoods_attribute");
        $result = $this->connectionFrom->query('SELECT * FROM attribute order by attr_id asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        $datetime = date('Y-m-d H:i:s');

        while ($item = $result->fetch()) {
            $_id = myMongoId(new \MongoId());
            $type_id = '';
            if (!empty($item->type_id)) {
                $typeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_type where shopnc_type_id={$item->type_id}", \Phalcon\Db::FETCH_ASSOC);
                $type_id = $typeInfo['_id'];
            }
            $success = $this->connectionTo->execute("INSERT INTO igoods_attribute(_id,name,type_id,attr_value,is_show,sort,__CREATE_TIME__,__MODIFY_TIME__,__REMOVED__,shopnc_attr_id,shopnc_type_id) VALUES (?,?,?,?,?,?,?,?,?,?,?)", array(
                $_id, // _id
                $item->attr_name, // name
                $type_id, // type_id
                $item->attr_value, // attr_value
                $item->attr_show, // is_show
                $item->attr_sort, // sort
                $datetime, // __CREATE_TIME__
                $datetime, // __MODIFY_TIME__
                0, // __REMOVED__
                $item->attr_id, // shopnc_attr_id
                $item->type_id // shopnc_type_id
            ));
        }
        echo "OK<br/>";

        return;
    }

    /**
     * 10.
     * 从shopnc的attribute_value表将数据导入到igoods_attribute_value表中
     */
    public function transferattributevalueAction()
    {
        // http://phalconm4local/service/database/transferattributevalue
        // http://phalconm/service/database/transferattributevalue

        // 从源表中获取数据
        $this->connectionTo->execute("Delete FROM igoods_attribute_value");
        $result = $this->connectionFrom->query('SELECT * FROM attribute_value order by attr_value_id asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        $datetime = date('Y-m-d H:i:s');

        while ($item = $result->fetch()) {
            $_id = myMongoId(new \MongoId());
            $type_id = '';
            if (!empty($item->type_id)) {
                $typeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_type where shopnc_type_id={$item->type_id}", \Phalcon\Db::FETCH_ASSOC);
                $type_id = $typeInfo['_id'];
            }

            $attr_id = '';
            if (!empty($item->attr_id)) {
                $attributeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_attribute where shopnc_attr_id={$item->attr_id}", \Phalcon\Db::FETCH_ASSOC);
                $attr_id = $attributeInfo['_id'];
            }

            $success = $this->connectionTo->execute("INSERT INTO igoods_attribute_value(_id,name,type_id,attr_id,sort,__CREATE_TIME__,__MODIFY_TIME__,__REMOVED__,shopnc_attr_value_id,shopnc_type_id,shopnc_attr_id) VALUES (?,?,?,?,?,?,?,?,?,?,?)", array(
                $_id, // _id
                $item->attr_value_name, // name
                $type_id, // type_id
                $attr_id, // attr_id,
                $item->attr_value_sort, // sort
                $datetime, // __CREATE_TIME__
                $datetime, // __MODIFY_TIME__
                0, // __REMOVED__
                $item->attr_value_id, // shopnc_attr_value_id
                $item->type_id, // shopnc_type_id
                $item->attr_id // shopnc_attr_id
            ));
        }
        echo "OK<br/>";

        return;
    }

    /**
     * 11.
     * 从shopnc的goods_class_tag表将数据导入到igoods_category_tag表中
     */
    public function transfergoodsclasstagAction()
    {
        // http://phalconm4local/service/database/transfergoodsclasstag
        // http://phalconm/service/database/transfergoodsclasstag

        // 从源表中获取数据
        $this->connectionTo->execute("Delete FROM igoods_category_tag");
        $result = $this->connectionFrom->query('SELECT * FROM goods_class_tag order by gc_tag_id asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        $datetime = date('Y-m-d H:i:s');

        while ($item = $result->fetch()) {
            $_id = myMongoId(new \MongoId());
            $type_id = '';
            if (!empty($item->type_id)) {
                $typeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_type where shopnc_type_id={$item->type_id}", \Phalcon\Db::FETCH_ASSOC);
                $type_id = $typeInfo['_id'];
            }

            $gc_id_1 = '';
            if (!empty($item->gc_id_1)) {
                $attributeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_category where shopnc_class_id={$item->gc_id_1}", \Phalcon\Db::FETCH_ASSOC);
                $gc_id_1 = $attributeInfo['_id'];
            }

            $gc_id_2 = '';
            if (!empty($item->gc_id_2)) {
                $attributeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_category where shopnc_class_id={$item->gc_id_2}", \Phalcon\Db::FETCH_ASSOC);
                $gc_id_2 = $attributeInfo['_id'];
            }

            $gc_id_3 = '';
            if (!empty($item->gc_id_3)) {
                $attributeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_category where shopnc_class_id={$item->gc_id_3}", \Phalcon\Db::FETCH_ASSOC);
                $gc_id_3 = $attributeInfo['_id'];
            }

            $gc_id = '';
            if (!empty($item->gc_id)) {
                $attributeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_category where shopnc_class_id={$item->gc_id}", \Phalcon\Db::FETCH_ASSOC);
                $gc_id = $attributeInfo['_id'];
            }

            $success = $this->connectionTo->execute("INSERT INTO igoods_category_tag(_id,gc_id_1,gc_id_2,gc_id_3,tag_name,tag_value,gc_id,type_id,__CREATE_TIME__,__MODIFY_TIME__,__REMOVED__,shopnc_gc_tag_id,shopnc_gc_id_1,shopnc_gc_id_2,shopnc_gc_id_3,shopnc_gc_id,shopnc_type_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array(
                $_id, // _id
                $gc_id_1, // gc_id_1
                $gc_id_2, // gc_id_2
                $gc_id_3, // gc_id_3,
                $item->gc_tag_name, // tag_name,
                $item->gc_tag_value, // tag_value,
                $gc_id, // gc_id,
                $type_id, // type_id
                $datetime, // __CREATE_TIME__
                $datetime, // __MODIFY_TIME__
                0, // __REMOVED__
                $item->gc_tag_id, // shopnc_gc_tag_id
                $item->gc_id_1, // shopnc_gc_id_1
                $item->gc_id_2, // shopnc_gc_id_2
                $item->gc_id_3, // shopnc_gc_id_3
                $item->gc_id, // shopnc_gc_id
                $item->type_id // shopnc_type_id
            ));
        }
        echo "OK<br/>";

        return;
    }

    /**
     * 12.
     * 从shopnc的goods_common表将数据导入到igoods_common表中
     */
    public function transfergoodscommonAction()
    {
        // http://phalconm4local/service/database/transfergoodscommon
        // http://phalconm/service/database/transfergoodscommon

        // 从源表中获取数据
        $this->connectionTo->execute("Delete FROM igoods_common");
        $result = $this->connectionFrom->query('SELECT * FROM goods_common order by goods_commonid asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        $datetime = date('Y-m-d H:i:s');

        while ($item = $result->fetch()) {
            $_id = myMongoId(new \MongoId());
            $store_id = '';
            $type_id = '';
            if (!empty($item->type_id)) {
                $typeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_type where shopnc_type_id={$item->type_id}", \Phalcon\Db::FETCH_ASSOC);
                $type_id = $typeInfo['_id'];
            }

            $gc_id = '';
            if (!empty($item->gc_id)) {
                $attributeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_category where shopnc_class_id={$item->gc_id}", \Phalcon\Db::FETCH_ASSOC);
                $gc_id = $attributeInfo['_id'];
            }

            $gc_id_1 = '';
            if (!empty($item->gc_id_1)) {
                $attributeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_category where shopnc_class_id={$item->gc_id_1}", \Phalcon\Db::FETCH_ASSOC);
                $gc_id_1 = $attributeInfo['_id'];
            }

            $gc_id_2 = '';
            if (!empty($item->gc_id_2)) {
                $attributeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_category where shopnc_class_id={$item->gc_id_2}", \Phalcon\Db::FETCH_ASSOC);
                $gc_id_2 = $attributeInfo['_id'];
            }

            $gc_id_3 = '';
            if (!empty($item->gc_id_3)) {
                $attributeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_category where shopnc_class_id={$item->gc_id_3}", \Phalcon\Db::FETCH_ASSOC);
                $gc_id_3 = $attributeInfo['_id'];
            }

            $brand_id = '';
            if (!empty($item->brand_id)) {
                $brandInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_brand where shopnc_brand_id={$item->brand_id}", \Phalcon\Db::FETCH_ASSOC);
                $brand_id = $brandInfo['_id'];
            }
            $success = $this->connectionTo->execute("INSERT INTO igoods_common(_id,name,jingle,gc_id,gc_id_1,gc_id_2,gc_id_3,gc_name,store_id,store_name,spec_name,spec_value,brand_id,brand_name,type_id,image,attr,body,mobile_body,state,stateremark,verify,verifyremark,is_lock,addtime,selltime,specname,price,marketprice,costprice,discount,serial,storage_alarm,transport_id,transport_title,commend,freight,vat,areaid_1,areaid_2,goods_stcids,plateid_top,plateid_bottom,is_virtual,virtual_indate,virtual_limit,virtual_invalid_refund,is_fcode,is_appoint,appoint_satedate,is_presell,presell_deliverdate,is_own_shop,__CREATE_TIME__,__MODIFY_TIME__,__REMOVED__,shopnc_goods_commonid,shopnc_gc_id,shopnc_gc_id_1,shopnc_gc_id_2,shopnc_gc_id_3,shopnc_store_id,shopnc_brand_id,shopnc_type_id,shopnc_transport_id,shopnc_areaid_1,shopnc_areaid_2,shopnc_plateid_top,shopnc_plateid_bottom) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array(
                $_id, // _id
                $item->goods_name, // name
                $item->goods_jingle, // jingle
                $gc_id, // gc_id,
                $gc_id_1, // gc_id_1,
                $gc_id_2, // gc_id_2,
                $gc_id_3, // gc_id_3,
                $item->gc_name, // gc_name,
                $store_id, // store_id,
                $item->store_name, // store_name,
                $item->spec_name, // spec_name,
                $item->spec_value, // spec_value,
                $brand_id, // brand_id,
                $item->brand_name, // brand_name,
                $type_id, // type_id,
                'goodscommon/' . $item->goods_image, // image,
                $item->goods_attr, // attr,
                $item->goods_body, // body,
                $item->mobile_body, // mobile_body,
                $item->goods_state, // state,
                $item->goods_stateremark, // stateremark,
                $item->goods_verify, // verify,
                $item->goods_verifyremark, // verifyremark,
                $item->goods_lock, // is_lock,
                $item->goods_addtime, // addtime,
                $item->goods_selltime, // selltime,
                $item->goods_specname, // specname,
                $item->goods_price, // price,
                $item->goods_marketprice, // marketprice,
                $item->goods_costprice, // costprice,
                $item->goods_discount, // discount,
                $item->goods_serial, // serial,
                $item->goods_storage_alarm, // storage_alarm,
                $item->transport_id, // transport_id,
                $item->transport_title, // transport_title,
                $item->goods_commend, // commend,
                $item->goods_freight, // freight,
                $item->goods_vat, // vat,
                $item->areaid_1, // areaid_1,
                $item->areaid_2, // areaid_2,
                $item->goods_stcids, // goods_stcids,
                $item->plateid_top, // plateid_top,
                $item->plateid_bottom, // plateid_bottom,
                $item->is_virtual, // is_virtual,
                $item->virtual_indate, // virtual_indate,
                $item->virtual_limit, // virtual_limit,
                $item->virtual_invalid_refund, // virtual_invalid_refund,
                $item->is_fcode, // is_fcode,
                $item->is_appoint, // is_appoint,
                $item->appoint_satedate, // appoint_satedate,
                $item->is_presell, // is_presell,
                $item->presell_deliverdate, // presell_deliverdate,
                $item->is_own_shop, // is_own_shop,
                $datetime, // __CREATE_TIME__
                $datetime, // __MODIFY_TIME__
                0, // __REMOVED__
                $item->goods_commonid, // shopnc_goods_commonid
                $item->gc_id, // shopnc_gc_id
                $item->gc_id_1, // shopnc_gc_id_1
                $item->gc_id_2, // shopnc_gc_id_2
                $item->gc_id_3, // shopnc_gc_id_3
                $item->store_id, // shopnc_store_id
                $item->brand_id, // shopnc_brand_id
                $item->type_id, // shopnc_type_id
                $item->transport_id, // shopnc_transport_id
                $item->areaid_1, // shopnc_areaid_1
                $item->areaid_2, // shopnc_areaid_2
                $item->plateid_top, // shopnc_plateid_top
                $item->plateid_bottom // shopnc_plateid_bottom
            ));
        }
        echo "OK<br/>";

        return;
    }

    /**
     * 13.
     * 从shopnc的goods表将数据导入到igoods_goods表中
     */
    public function transfergoodsAction()
    {
        // http://phalconm4local/service/database/transfergoods
        // http://phalconm/service/database/transfergoods

        // 从源表中获取数据
        $this->connectionTo->execute("Delete FROM igoods_goods");
        $result = $this->connectionFrom->query('SELECT * FROM goods order by goods_id asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        $datetime = date('Y-m-d H:i:s');

        while ($item = $result->fetch()) {
            $_id = myMongoId(new \MongoId());
            $store_id = '';
            $type_id = '';
            if (!empty($item->type_id)) {
                $typeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_type where shopnc_type_id={$item->type_id}", \Phalcon\Db::FETCH_ASSOC);
                $type_id = $typeInfo['_id'];
            }
            $goods_commonid = '';
            if (!empty($item->goods_commonid)) {
                $goodsCommonInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_common where shopnc_goods_commonid={$item->goods_commonid}", \Phalcon\Db::FETCH_ASSOC);
                $goods_commonid = $goodsCommonInfo['_id'];
            }
            $gc_id = '';
            if (!empty($item->gc_id)) {
                $attributeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_category where shopnc_class_id={$item->gc_id}", \Phalcon\Db::FETCH_ASSOC);
                $gc_id = $attributeInfo['_id'];
            }

            $gc_id_1 = '';
            if (!empty($item->gc_id_1)) {
                $attributeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_category where shopnc_class_id={$item->gc_id_1}", \Phalcon\Db::FETCH_ASSOC);
                $gc_id_1 = $attributeInfo['_id'];
            }

            $gc_id_2 = '';
            if (!empty($item->gc_id_2)) {
                $attributeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_category where shopnc_class_id={$item->gc_id_2}", \Phalcon\Db::FETCH_ASSOC);
                $gc_id_2 = $attributeInfo['_id'];
            }

            $gc_id_3 = '';
            if (!empty($item->gc_id_3)) {
                $attributeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_category where shopnc_class_id={$item->gc_id_3}", \Phalcon\Db::FETCH_ASSOC);
                $gc_id_3 = $attributeInfo['_id'];
            }

            $brand_id = '';
            if (!empty($item->brand_id)) {
                $brandInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_brand where shopnc_brand_id={$item->brand_id}", \Phalcon\Db::FETCH_ASSOC);
                $brand_id = $brandInfo['_id'];
            }
            $success = $this->connectionTo->execute("INSERT INTO igoods_goods(_id,goods_commonid,name,jingle,store_id,store_name,gc_id,gc_id_1,gc_id_2,gc_id_3,brand_id,price,promotion_price,promotion_type,marketprice,serial,storage_alarm,click,salenum,collect,spec,storage,image,state,verify,addtime,edittime,areaid_1,areaid_2,color_id,transport_id,freight,vat,commend,stcids,evaluation_good_star,evaluation_count,is_virtual,virtual_indate,virtual_limit,virtual_invalid_refund,is_fcode,is_appoint,is_presell,have_gift,is_own_shop,__CREATE_TIME__,__MODIFY_TIME__,__REMOVED__,shopnc_goods_id,shopnc_goods_commonid,shopnc_store_id,shopnc_gc_id,shopnc_gc_id_1,shopnc_gc_id_2,shopnc_gc_id_3,shopnc_brand_id,shopnc_areaid_1,shopnc_areaid_2,shopnc_color_id,shopnc_transport_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array(
                $_id, // _id
                $goods_commonid, // goods_commonid
                $item->goods_name, // name
                $item->goods_jingle, // jingle
                $store_id, // store_id,
                $item->store_name, // store_name,
                $gc_id, // gc_id,
                $gc_id_1, // gc_id_1,
                $gc_id_2, // gc_id_2,
                $gc_id_3, // gc_id_3,
                $brand_id, // brand_id,
                $item->goods_price, // price,
                $item->goods_promotion_price, // promotion_price,
                $item->goods_promotion_type, // promotion_type,
                $item->goods_marketprice, // marketprice,
                $item->goods_serial, // serial,
                $item->goods_storage_alarm, // storage_alarm,
                $item->goods_click, // click,
                $item->goods_salenum, // salenum,
                $item->goods_collect, // collect,
                $item->goods_spec, // spec,
                $item->goods_storage, // storage,
                'goods/' . $item->goods_image, // image,
                $item->goods_state, // state,
                $item->goods_verify, // verify,
                $item->goods_addtime, // addtime,
                $item->goods_edittime, // edittime,
                $item->areaid_1, // areaid_1,
                $item->areaid_2, // areaid_2,
                $item->color_id, // color_id,
                $item->transport_id, // transport_id,
                $item->goods_freight, // freight,
                $item->goods_vat, // vat,
                $item->goods_commend, // commend,
                $item->goods_stcids, // goods_stcids,
                $item->evaluation_good_star, // evaluation_good_star,
                $item->evaluation_count, // evaluation_count,
                $item->is_virtual, // is_virtual,
                $item->virtual_indate, // virtual_indate,
                $item->virtual_limit, // virtual_limit,
                $item->virtual_invalid_refund, // virtual_invalid_refund,
                $item->is_fcode, // is_fcode,
                $item->is_appoint, // is_appoint,
                $item->is_presell, // is_presell,
                $item->have_gift, // have_gift,
                $item->is_own_shop, // is_own_shop,
                $datetime, // __CREATE_TIME__
                $datetime, // __MODIFY_TIME__
                0, // __REMOVED__
                $item->goods_id, // shopnc_goods_id
                $item->goods_commonid, // shopnc_goods_commonid
                $item->store_id, // shopnc_store_id
                $item->gc_id, // shopnc_gc_id
                $item->gc_id_1, // shopnc_gc_id_1
                $item->gc_id_2, // shopnc_gc_id_2
                $item->gc_id_3, // shopnc_gc_id_3
                $item->brand_id, // shopnc_brand_id
                $item->areaid_1, // shopnc_areaid_1
                $item->areaid_2, // shopnc_areaid_2
                $item->color_id, // shopnc_color_id
                $item->transport_id // shopnc_transport_id
            ));
        }
        echo "OK<br/>";

        return;
    }

    /**
     * 14.
     * 从shopnc的goods_attr_index表将数据导入到igoods_attr_index表中
     */
    public function transfergoodsattrindexAction()
    {
        // http://phalconm4local/service/database/transfergoodsattrindex
        // http://phalconm/service/database/transfergoodsattrindex

        // 从源表中获取数据
        $this->connectionTo->execute("Delete FROM igoods_attr_index");
        $result = $this->connectionFrom->query('SELECT * FROM goods_attr_index order by goods_id asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        $datetime = date('Y-m-d H:i:s');

        while ($item = $result->fetch()) {
            $_id = myMongoId(new \MongoId());
            $type_id = '';
            if (!empty($item->type_id)) {
                $typeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_type where shopnc_type_id={$item->type_id}", \Phalcon\Db::FETCH_ASSOC);
                $type_id = $typeInfo['_id'];
            }

            $attr_id = '';
            if (!empty($item->attr_id)) {
                $attributeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_attribute where shopnc_attr_id={$item->attr_id}", \Phalcon\Db::FETCH_ASSOC);
                $attr_id = $attributeInfo['_id'];
            }

            $goods_id = '';
            if (!empty($item->goods_id)) {
                $goodsInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_goods where shopnc_goods_id={$item->goods_id}", \Phalcon\Db::FETCH_ASSOC);
                $goods_id = $goodsInfo['_id'];
            }

            $goods_commonid = '';
            if (!empty($item->goods_commonid)) {
                $goodsCommonInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_common where shopnc_goods_commonid={$item->goods_commonid}", \Phalcon\Db::FETCH_ASSOC);
                $goods_commonid = $goodsCommonInfo['_id'];
            }

            $attr_value_id = '';
            if (!empty($item->attr_value_id)) {
                $attributeValueInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_attribute_value where shopnc_attr_value_id={$item->attr_value_id}", \Phalcon\Db::FETCH_ASSOC);
                $attr_value_id = $attributeValueInfo['_id'];
            }

            $gc_id = '';
            if (!empty($item->gc_id)) {
                $attributeInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_category where shopnc_class_id={$item->gc_id}", \Phalcon\Db::FETCH_ASSOC);
                $gc_id = $attributeInfo['_id'];
            }

            $success = $this->connectionTo->execute("INSERT INTO igoods_attr_index(_id,goods_id,goods_commonid,gc_id,type_id,attr_id,attr_value_id,__CREATE_TIME__,__MODIFY_TIME__,__REMOVED__,shopnc_goods_id,shopnc_goods_commonid,shopnc_gc_id,shopnc_type_id,shopnc_attr_id,shopnc_attr_value_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array(
                $_id, // _id
                $goods_id, // goods_id
                $goods_commonid, // goods_commonid
                $gc_id, // gc_id
                $type_id, // type_id
                $attr_id, // attr_id,
                $attr_value_id, // attr_value_id
                $datetime, // __CREATE_TIME__
                $datetime, // __MODIFY_TIME__
                0, // __REMOVED__
                $item->goods_id, // shopnc_goods_id
                $item->goods_commonid, // shopnc_goods_commonid
                $item->gc_id, // shopnc_gc_id
                $item->type_id, // shopnc_type_id
                $item->attr_id, // shopnc_attr_id
                $item->attr_value_id // shopnc_attr_value_id
            ));
        }
        echo "OK<br/>";

        return;
    }

    /**
     * 15.
     * 从shopnc的goods_fcode表将数据导入到igoods_fcode表中
     */
    public function transfergoodsfcodeAction()
    {
        // http://phalconm4local/service/database/transfergoodsfcode
        // http://phalconm/service/database/transfergoodsfcode

        // 从源表中获取数据
        $this->connectionTo->execute("Delete FROM igoods_fcode");
        $result = $this->connectionFrom->query('SELECT * FROM goods_fcode order by fc_id asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        $datetime = date('Y-m-d H:i:s');

        while ($item = $result->fetch()) {
            $_id = myMongoId(new \MongoId());

            $goods_commonid = '';
            if (!empty($item->goods_commonid)) {
                $goodsCommonInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_common where shopnc_goods_commonid={$item->goods_commonid}", \Phalcon\Db::FETCH_ASSOC);
                $goods_commonid = $goodsCommonInfo['_id'];
            }

            $success = $this->connectionTo->execute("INSERT INTO igoods_fcode(_id,goods_commonid,code,state,__CREATE_TIME__,__MODIFY_TIME__,__REMOVED__,shopnc_fc_id,shopnc_goods_commonid) VALUES (?,?,?,?,?,?,?,?,?)", array(
                $_id, // _id
                $goods_commonid, // goods_commonid
                $item->fc_code, // code
                $item->fc_state, // state
                $datetime, // __CREATE_TIME__
                $datetime, // __MODIFY_TIME__
                0, // __REMOVED__
                $item->fc_id, // shopnc_fc_id
                $item->goods_commonid // shopnc_goods_commonid
            ));
        }
        echo "OK<br/>";

        return;
    }

    /**
     * 16.
     * 从shopnc的goods_images表将数据导入到igoods_images表中
     */
    public function transfergoodsimagesAction()
    {
        // http://phalconm4local/service/database/transfergoodsimages
        // http://phalconm/service/database/transfergoodsimages

        // 从源表中获取数据
        $this->connectionTo->execute("Delete FROM igoods_images");
        $result = $this->connectionFrom->query('SELECT * FROM goods_images order by goods_image_id asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        $datetime = date('Y-m-d H:i:s');

        while ($item = $result->fetch()) {
            $_id = myMongoId(new \MongoId());
            $store_id = '';
            $color_id = '';
            $goods_commonid = '';
            if (!empty($item->goods_commonid)) {
                $goodsCommonInfo = $this->connectionTo->fetchOne("SELECT * FROM igoods_common where shopnc_goods_commonid={$item->goods_commonid}", \Phalcon\Db::FETCH_ASSOC);
                $goods_commonid = $goodsCommonInfo['_id'];
            }

            $success = $this->connectionTo->execute("INSERT INTO igoods_images(_id,goods_commonid,store_id,color_id,image,sort,is_default,__CREATE_TIME__,__MODIFY_TIME__,__REMOVED__,shopnc_goods_image_id,shopnc_goods_commonid,shopnc_store_id,shopnc_color_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array(
                $_id, // _id
                $goods_commonid, // goods_commonid
                $store_id, // store_id
                $color_id, // color_id
                'fcode/' . $item->goods_image, // image
                $item->goods_image_sort, // sort
                $item->is_default, // is_default
                $datetime, // __CREATE_TIME__
                $datetime, // __MODIFY_TIME__
                0, // __REMOVED__
                $item->goods_image_id, // shopnc_goods_image_id
                $item->goods_commonid, // shopnc_goods_commonid
                $item->store_id, // shopnc_store_id
                $item->color_id // shopnc_color_id
            ));
        }
        echo "OK<br/>";

        return;
    }

    /**
     * 20.
     * 从shopnc的goods_images表将数据导入到igoods_images表中
     */
    public function areaparentcodeAction()
    {
        // http://phalconm4local/service/database/areaparentcode
        // http://phalconm/service/database/areaparentcode
        // http://www.jizigou.com/service/database/areaparentcode

        // 从源表中获取数据
        $result = $this->connectionTo->query('SELECT * FROM area order by level desc,code asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        while ($item = $result->fetch()) {
            $_id = $item->_id;
            $code = substr($item->code, 0, 3);
            $parent_name = $item->parent_name;
            $level = $item->level;
            $parent_code = $item->parent_code;
            if (!empty($parent_code)) {
                continue;
            }
            if (!empty($parent_name)) {
                if ($level > 3) {
                    $areaInfo = $this->connectionTo->query('SELECT * FROM area where name=? and level=? and LEFT(code, 3)=?', array(
                        $parent_name,
                        $level - 1,
                        $code
                    ));
                } else {
                    $areaInfo = $this->connectionTo->query('SELECT * FROM area where name=? and level=?', array(
                        $parent_name,
                        $level - 1
                    ));
                }
                if (!empty($areaInfo)) {
                    $areaInfo = $areaInfo->fetch();
                    $parent_code = $areaInfo['code'];
                }
            }
            if (!empty($parent_code)) {
                $success = $this->connectionTo->execute("update area set parent_code=? where _id=?", array(
                    $parent_code,
                    $_id
                ));
            }
        }
        echo "OK<br/>";

        return;
    }

    /**
     * 从shopnc的express表中将数据导入到ifreight_express
     */
    public function transferexpressAction()
    {
        // http://phalconm4local/service/database/transferexpress
        // http://phalconm/service/database/transferexpress

        // 从源表中获取数据
        $this->connectionTo->execute("Delete FROM ifreight_express");
        $result = $this->connectionFrom->query('SELECT * FROM express order by id asc', null);
        $result->setFetchMode(\Phalcon\Db::FETCH_OBJ);

        $datetime = date('Y-m-d H:i:s');

        while ($item = $result->fetch()) {
            $_id = myMongoId(new \MongoId());
            $parent_id = '';
            $type_id = '';
            $success = $this->connectionTo->execute("INSERT INTO ifreight_express(_id,name,state,code,letter,is_order,url,zt_state,__CREATE_TIME__,__MODIFY_TIME__,__REMOVED__) VALUES (?,?,?,?,?,?,?,?,?,?,?)", array(
                $_id, // _id
                $item->e_name, // name
                $item->e_state, // e_state
                $item->e_code, // e_code
                $item->e_letter, // e_letter
                $item->e_order, // e_order
                $item->e_url, // e_url
                $item->e_zt_state, // e_zt_state
                $datetime, // __CREATE_TIME__
                $datetime, // __MODIFY_TIME__
                0 // __REMOVED__
            ));
        }
        echo "OK<br/>";

        return;
    }
}
