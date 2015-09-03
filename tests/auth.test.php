<?php

require_once 'classes/auth.class.php';

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class AuthTest extends PHPUnit_Framework_TestCase
{
  private $auth;
  private $usr;
  private $pdo;

  public function setUp() {
    $this->usr = $this->getMockBuilder('User')
                ->setMethods(array('import', 'create'))
                ->getMock();
    $this->usr_gw = $this->getMockBuilder('UserGateway')
                ->setMethods(array('existsBy'))
                ->getMock();
    $this->usr_session = $this->getMockBuilder('Sesh')
                ->getMock();                
    $this->auth = new Auth($this->usr, $this->usr_session, $this->usr_gw);
  }    

  public function testCreateUserCallsUserCreate() {
    $this->usr_gw->method('existsBy')->willReturn(false);
    $this->usr->expects($this->once())
      ->method('create');
    $this->auth->createUser("jimmy@gmail.com", "passwordABC");
  }

 public function testCreateUserReturnsSuccess() {
    $this->usr_gw->method('existsBy')->willReturn(false);
    $this->usr->method('create')
      ->willReturn(true);
    $authcode = $this->auth->createUser("jimmy@gmail.com", "passwordABC");
    $this->assertEquals(Auth::SUCCESSFUL, $authcode);
  }

  public function testDuplicateUserShouldFail() {
    $this->usr_gw->method('existsBy')->willReturn(true);
    $this->usr->method('create')
      ->willReturn(true);
    $authcode = $this->auth->createUser("jimmy@gmail.com", "passwordABC");
    $this->assertEquals(Auth::USER_EXISTS, $authcode);
  }

}
