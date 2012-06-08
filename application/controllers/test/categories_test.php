<?php
require_once(APPPATH . '/controllers/test/Toast.php');

class Categories_test extends Toast
{

    var $m_all_count;
    var $m_category_count;
    var $m_id;
    var $m_category_id;
    var $m_form_data;

	function __construct() // change constructor method name
	{
	   parent::__construct(__FILE__); // Remember this
	   
       $this->load->model('category');

       $this->m_category_id = NULL;

       $this->m_form_data = array(
            'name'          =>      'TEST_name',
            'category_id'   =>      NULL
        );

	}

    /**
     * OPTIONAL; Anything in this function will be run before each test
     * Good for doing cleanup: resetting sessions, renewing objects, etc.
     */
    function _pre() {}

    /**
     * OPTIONAL; Anything in this function will be run after each test
     * I use it for setting $this->message = $this->My_model->getError();
     */
    function _post() {}


    function test_pre_count()
    {   
        $this->m_all_count = Category::count();
    }


	function test_create()
	{
        $value = Category::create($this->m_form_data) ;
        
            $this->m_id                 = Category::last()->id;
            $this->m_category_count     = Category::count_by_category($this->m_form_data['category_id']); 
        
        $this->_assert_true($value);  

    }


    function test_check_create_name()
    {
        $this->_assert_equals( $this->m_form_data['name'], Category::find($this->m_id)->name );  
    }


    function test_check_create_category_id()
    {
        $this->_assert_equals( $this->m_category_id, Category::find($this->m_id)->category_id );  
    }


    function test_order_correct()
    {
        $category = Category::find($this->m_id);
        $this->_assert_equals($category->orden, $this->m_category_count);
    }


    function test_post_count_create()
    {
        $this->_assert_equals($this->m_all_count + 1, Category::count());   
    }


    function test_dont_create_same_name_that_other()
    {
        $form_data_2 = array(
            'name'          =>      'TEST_name',
            'category_id'   =>      NULL
        );

        $category = Category::create($form_data_2) ;

        $this->_assert_false($category->is_valid()) ;
        
    }


    function test_dont_edit_same_name_that_other()
    {
        $form_data_2 = array(
            'name'          =>      'TEST_name_2',
            'category_id'   =>      NULL
        );

        Category::create($form_data_2);

        $category2 = Category::last();

        $category2->update_attributes( array('name' =>  'TEST_name') );

        $category2->delete();

        $this->_assert_false($category2->is_valid()) ;      
    }


    function test_dont_change_order_on_edit()
    {
        $category = Category::find($this->m_id);

        $orden_first = $category->orden;

        $form_data_2 = array(
            'name'          =>      'TEST_name_22',
            'category_id'   =>      NULL
        );

        Category::create($form_data_2);

        $category3 = Category::last();

        $orden_first = $category->orden;

        $category->update_attributes( array('name' =>  'TEST_name') );

        $orden_last = $category->orden;

        $category3->delete();

        $this->_assert_equals($orden_first, $orden_last);
    }
    

    function test_dont_blank_name()
    {
        $form_data_2 = array(
            'name'          =>  '',
            'category_id'   =>   NULL
        );

        $category = new Category($form_data_2);  
        $this->_assert_false($category->is_valid()) ;           
    }


    function test_delete()
    {

        $category = Category::find($this->m_id);

        if ($category->delete()  == TRUE)
        {
            $this->_assert_true(TRUE);   
        }
        else
        {
            $this->_assert_true(FALSE); 
        }
    }


    function test_post_count_delete()
    {
        $this->_assert_equals($this->m_all_count, Category::count());  
    }


}
	