<?php
class AuthTest extends PHPUnit_Framework_TestCase
{
  public function testCreateUser() {

    $mockdb = $this->getMockBuilder('myDb')
      ->setMethods(array('insert'))
      ->getMock();

    $mockdb->expects($this->once())
      ->method('insert')
      ->with($this->equalTo('users'));

    // create user
    $auth = new Auth($mockdb);
    $auth->createUser("jimmy", "password");
    
  }
}
?>
