<?php 


// http://mvc.loc/register/[login]
class RegisterController extends Controller 
{

      /**
       * Constructor
       * @param string $controller 
       * @param string $action 
       * @return void
      */
      public function __construct($controller, $action)
      {
           parent::__construct($controller, $action);
           $this->loadModel('Users'); 
           $this->view->setLayout('default');
      }


      /**
       * Login action
       * @return void
      */
  	  public function loginAction()
  	  {
           $validation = new Validate();
           
           if($_POST)
           {
                // form validation
                $validation->check($_POST, [
                    'username' => [
                        'display'  => "Username",  
                        'required' => true,   
                    ], 
                    'password' => [
                         'display' => 'Password',
                         'required' => true,
                         'min' => 6
                    ]
                ], true); // true for csrf Token


                if($validation->passed())
                {
                     $user = $this->UsersModel->findByUsername($_POST['username']);
                     
                     if($user && password_verify(Input::get('password'), $user->password))
                     {
                          $remember = (isset($_POST['remember_me']) && Input::get('remember_me')) ? true : false;

                          $user->login($remember);
                          Router::redirect(''); // redirect to home page '/'

                     }else{

                         $validation->addError("There is an error with your username or password.");
                     }
                }
           }

           $this->view->displayErrors = $validation->displayErrors();
           $this->view->render('register/login');
  	  }


      /**
       * Logout action
       * @return void
      */
      public function logoutAction()
      {
         if(currentUser())
         {
              currentUser()->logout();
         }

         Router::redirect('register/login');
      }

      
      /**
       * Register action
       * @return void
      */
      public function registerAction()
      {
         $validation = new Validate();

         $posted_values = [
           'fname'    => '', 
           'lname'    => '', 
           'username' => '',
           'email'    => '',
           'password' => '',
           'confirm'  => '',
         ];


         if($_POST)
         {
             $posted_values = posted_values($_POST);

             $validation->check($_POST, [
                'fname'    => [
                    'display'  => 'First Name',  
                    'required' => true 
                ],  
                'lname'    => [
                    'display'  => 'Last Name',  
                    'required' => true 
                ],  
                'username' => [
                    'display'  => 'Username',  
                    'required' => true, 
                    'unique'   => 'users',
                    'min' => 6,
                    'max' => 150
                ],  
                'email'    => [
                    'display'  => 'Email',  
                    'required' => true, 
                    'unique'   => 'users',
                    'max' => 150,
                    'valid_email' => true
                ],  
                'password' => [
                    'display'  => 'Password',  
                    'required' => true, 
                    'min' => 6,
                ],  
                'confirm'  => [
                    'display'  => 'Confirm Password',  
                    'required' => true, 
                    'matches' => 'password'
                ]
             ], true);
         }

         
         if($validation->passed())
         {
             $newUser = new Users();
             $newUser->registerNewUser($_POST);
             Router::redirect('register/login');
         }


         $this->view->post = $posted_values;
         $this->view->displayErrors = $validation->displayErrors();
         $this->view->render('register/register');
      }
}