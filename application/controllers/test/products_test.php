<?php
require_once(APPPATH . '/controllers/test/Toast.php');

class Products_test extends Toast
{
    var $m_category;
    var $m_product;
    var $m_category_products_count;


	function __construct() // change constructor method name
	{
        parent::__construct(__FILE__); // Remember this

        //creamos una categoria de test
        $this->load->model('category');

        $parent_id = NULL;

        $cat_attributes = array(
            'name'          =>      'TEST_name',
            'category_id'   =>      $parent_id
        );

        Category::create($cat_attributes) ;

        $this->m_category = Category::find_by_id(Category::last()->id);

        $this->load->model('product');

        //buscamos cuantos productos hay de dicha categoria
        $this->m_category_products_count     = Product::count_by_category_id($this->m_category->id); 
    }



    function _pre() {}


    function _post() {}


    function test_create_product()
    {
        $form_data = array(
            'name'          =>  'Product test 1',
            'description'   =>  'Test Description, Test Description',
            'active'        =>  '1',
            'option'        =>  '1',
            'category_id'   =>  $this->m_category->id,
            'image'         =>  "srv/www/dfjdsf.jpg"
        );

        $product = Product::create($form_data);

        $this->m_product = Product::find_by_id(Product::last()->id);

        if ($product->is_valid())
            $this->_assert_true(TRUE);
        
        if ($product->is_invalid())
            $this->_assert_true(FALSE);
          
    }


    function test_create_product_without_name()
    {
        $form_data = array(
            'name'          =>  '',
            'description'   =>  'Test Description, Test Description',
            'active'        =>  '1',
            'option'        =>  '1',
            'category_id'   =>  $this->m_category->id,
            'image'         =>  "srv/www/dfjdsf.jpg"
        );

        $product = Product::create($form_data);


        if ($product->is_valid())
            $this->_assert_true(FALSE);
        
        if ($product->is_invalid())
            $this->_assert_true(TRUE);
          
    }    

    function test_create_product_without_category()
    {
        $form_data = array(
            'name'          =>  'Product Test-2',
            'description'   =>  'Test Description, Test Description',
            'active'        =>  '1',
            'option'        =>  '1',
            'category_id'   =>  NULL,
            'image'         =>  "srv/www/dfjdsf.jpg"
        );

        $product = Product::create($form_data);


        if ($product->is_valid())
            $this->_assert_true(FALSE);
        
        if ($product->is_invalid())
            $this->_assert_true(TRUE);
          
    }


        function test_create_product_without_img()
    {
        $form_data = array(
            'name'          =>  'Product Test -2',
            'description'   =>  'Test Description, Test Description',
            'active'        =>  '1',
            'option'        =>  '1',
            'category_id'   =>  $this->m_category->id,
            'image'         =>  ""
        );

        $product = Product::create($form_data);


        if ($product->is_valid())
            $this->_assert_true(FALSE);
        
        if ($product->is_invalid())
            $this->_assert_true(TRUE);
          
    }


    function test_dont_create_with_same_category_id(){
        
        $form_data = array(
            'name'          =>  'Product test 1',
            'description'   =>  'Test Description, Test Description',
            'active'        =>  '1',
            'option'        =>  '1',
            'category_id'   =>  $this->m_category->id,
            'image'         =>  "srv/www/dfjdsf.jpg"
        );

        $product = Product::create($form_data);

        $this->_assert_false($product->is_valid());
    }


    function test_delete_product()
    {
        if ($this->m_product->delete() == TRUE)
        {
            $this->_assert_true(TRUE);   
        }
        else
        {
            $this->_assert_true(FALSE); 
        }
    } 


    function test_delete_category()
    {
        if ($this->m_category->delete() == TRUE)
        {
            $this->_assert_true(TRUE);   
        }
        else
        {
            $this->_assert_true(FALSE); 
        }
    }   

}
	