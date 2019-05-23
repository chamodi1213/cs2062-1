<?php 
	/**
	 * 
	 */
	class Notification extends Model
	{
		protected $to, $from, $state, $product, $shop;
		public function __construct($table=''){
			$table='notification';
			parent::__construct($table);
		}

		public function createNotification($product_id,$tailor_id,$customer_id){

			$fields=['_to'=>$tailor_id,
					 '_from'=>$customer_id
					 ,'pr_id'=>$product_id,
					 'type'=>'',
					 'status'=> '1',
					 'seen'=> '0'
					];

			$this->insert($fields);			 
		}



		public function update($product_id,$tailor_id,$customer_id){
			$this->createNotification($product_id,$tailor_id,$customer_id);
		}

		public function getNewNoti( $id ){
			//unseen notifications
			$condition = array('conditions'=>['seen = ?','_to = ?'],'bind'=> [ '0' , $id]);	
			$list=$this->find($condition);		

			if (count($list)){
				foreach($list as $row){
					if ($row->type==1){
						$product=new CustomRequest();
						$row->pr_name=$product->findByID($row->pr_id)->pr_name;
					}
					else{
						$product=new Product();
						$row->pr_name=$product->findByID($row->pr_id)->name;
					}
					
					$tailorShop=new TailorShop();
					$row_new=$tailorShop->findById($row->_from);
					$row->shop=$row_new->name;

					$user=new User();
					$new_row=$user->findByUserID($row->_from);
					$row->from_name=$new_row->first_name.' '.$new_row->last_name;


				}
			}	

			return $list;
		}
		
		public function getSeenNoti( $id ){			
			//seen notifications
			$condition =array('conditions'=>['seen = ?','_to = ?'],'bind'=> [ '1' , $id]);
			$list= $this->find($condition);

			if (count($list)){
				foreach($list as $row){
					if ($row->type==1){
						$product=new CustomRequest();
						$row->pr_name=$product->findByID($row->pr_id)->pr_name;
					}

					else{
						$product=new Product();
						$row->pr_name=$product->findByID($row->pr_id)->name;
					}
					
					$tailorShop=new TailorShop();
					$row_new=$tailorShop->findById($row->_from);
					$row->shop=$row_new->name;

					$user=new User();
					$new_row=$user->findByUserID($row->_from);
					$row->from_name=$new_row->first_name.' '.$new_row->last_name;


				}
			}	

			return $list;
		}

		public function remove($id){
			$this-> delete($id);
		}

		public function updateAsSeen($id){
			$field=['seen'=>'1'];
			$this->update($id, $field);
		}
	}
 ?>