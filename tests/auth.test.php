<?php

require_once 'classes/auth.class.php';

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class AuthTest extends PHPUnit_Framework_TestCase
{
  public function setUp() {
    $this->usr = $this->getMockBuilder('User')
                ->setMethods(array('import', 'create'))
                ->getMock();
    $this->usr_gw = $this->getMockBuilder('UserGateway')
                ->setMethods(array('findBy'))
                ->getMock();
    $this->usr_session = $this->getMockBuilder('Sesh')
                ->getMock();                
    $this->auth = new Auth($this->usr, $this->usr_session, $this->usr_gw);
  }    

  public function testCreateUserShouldCallUserCreate() {
    $this->usr_gw->method('findBy')->willReturn(false);
    $this->usr->expects($this->once())
      ->method('create');
    $this->auth->createUser("jimmy@gmail.com", "passwordABC");
  }

 public function testCreateUserShouldReturnsSuccess() {
    $this->usr_gw->method('findBy')->willReturn(false);
    $this->usr->method('create')
      ->willReturn(true);
    $authcode = $this->auth->createUser("jimmy@gmail.com", "passwordABC");
    $this->assertEquals(Auth::SUCCESSFUL, $authcode);
  }

  public function testCreateDuplicateUserShouldFail() {
    $this->usr_gw->method('findBy')->willReturn(true);
    $this->usr->method('create')
      ->willReturn(true);
    $authcode = $this->auth->createUser("jimmy@gmail.com", "passwordABC");
    $this->assertEquals(Auth::USER_EXISTS, $authcode);
  }

  public function testCreateUserWithInvalidEmailShouldFail() {

  }

  public function testLoginValidShouldReturnSuccess() {

  }

  public function testLoginValidShouldCallSessionCreate() {

  }

  public function testLoginValidButFailedSessionCreateShouldReturnError() {

  }

  public function testLoginInvalidShouldFail() {

  }

  public function testCurrentUserShouldCallUserImport() {
    // test if id is correct also

  }

  public function testCurrentUserShouldReturnUser() {
    
  }

  public function testCurrentUserCalledAgainShouldReturnCurrentUserWithoutCallingImport() {

  } 
}
