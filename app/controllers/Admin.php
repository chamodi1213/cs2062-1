<?php
	class Admin extends Controller {
 
		public function __construct($controller, $action){
			parent::__construct($controller, $action);
		}

		public function newProductsAction(){

			$product = new Product();
        	$fields = [
        		'conditions' => 'permission = ?',
        		'bind' => ['0']
        	];
        	$params['products'] = $product->find($fields);
        	foreach ($params['products'] as $product) {

        		//bind images
        		$images = new TailorProductImage();
        		$fields = [
        			'conditions' => 'product_id = ?',
        			'bind' => [$product->id]
        		];
        		$images = $images->find($fields);
        		$product->images = $images;

        		//bind store logo
        		$store = new TailorShop();
        		$store = $store->getStoreByVendor($product->vendor_id);
        		$product->logo = $store->logo;

        		//set rating
        		$rating = new Rate();
        		$avgRating = $rating->calculateAvg($product->id);
        		$product->rating = $avgRating;
        		$product->ratingCount = $rating->getRateCount($product->id);

        		//set location
        		$product->streetName = $store->streetName2;
        		$product->city = $store->city;

        	}

            $this->view->render('admin/newProducts', $params);

		}


        public function approvePageAction($product_id){
            
            $params = array();

            //get product
            $product = new Product();
            $product_obj = $product->findById($product_id);

            //load sub categories table and instert sub category name into product
            $sub_category_obj = new SubCategory();
            $sub_category_details = $sub_category_obj->findByID($product_obj->sub_category_id);
            $product_obj->sub_category_name = $sub_category_details->name;

            //load categories table and instert main category name -> product_obj
            $category_obj = new Category();
            $category_details = $category_obj->findByID($sub_category_details->main_id);
            $product_obj->main_category_name = $category_details->name;
            $params['product'] = $product_obj;

            //add product images array - inster to params
            $img = new Image();
            $images = $img->getImage($product_id);
            $params['images'] = $images;

            //new user object
            $user_obj = new User();

            //add vendor name
            $product_obj->vendor = $user_obj->findByUserID($product_obj->vendor_id);

            //load product colors
            $color = new Color();
            $params['colors'] = $color->getColorByproductID($p_id);

            //load product measurements
            $measurement = new Measurement();
            $params['measurements'] = $measurement->getMeasurementByID($p_id);


            //dnd($params['product']->main_category_name);
            $this->view->render('admin/approvePage',$params);
        }


        public function newsletterAction(){
            $this->view->render('admin/newsletter');
        }

        public function sendNewsletterAction(){
            $admin = new SystemAdmin(currentUser()->id);
            $admin->sendNewsletter($_POST['subject'], $_POST['content']);
            Router::redirect('admin/newsletterSuccess');
        }

        public function newsletterSuccessAction(){
            $this->view->render('admin/newsletterSuccess');
        }

	}