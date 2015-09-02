<?php

require_once 'auth.class.php';
require_once 'connectionfactory.class.php';

// http://code.tutsplus.com/tutorials/evolving-toward-a-persistence-layer--net-27138
/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class AuthTest extends PHPUnit_Framework_TestCase
{
  private $auth;
  private $usr;
  private $pdo;

  // The setUp() and tearDown() template methods are run once for each test method (and on fresh instances) of the test case class.
  public function setUp() {
    $this->pdo = ConnectionFactory::getFactory()->getConnection();
    $this->usr = $this->getMockBuilder('User')
                ->setMethods(array('import', 'create'))
                ->getMock();
    $this->usr_gw = $this->getMockBuilder('UserGateway')
                ->getMock();
    $this->usr_session = $this->getMockBuilder('Wendell')
                ->getMock();                
    $this->auth = new Auth($this->usr, $this->usr_session, $this->usr_gw);
  }    

  public function testCreateUserCallsUserCreate() {
    $this->cleanUpDatabase();
    $this->usr_gw->method('existsBy')->willReturn(false);
    $this->usr->expects($this->once())
      ->method('create');
    $this->auth->createUser("jimmy@gmail.com", "passwordABC");
  }

 public function testCreateUserReturnsSuccess() {
    $this->cleanUpDatabase();
    $this->usr_gw->method('existsBy')->willReturn(false);
    $this->usr->method('create')
      ->willReturn(true);
    $authcode = $this->auth->createUser("jimmy@gmail.com", "passwordABC");
    $this->assertEquals(Auth::SUCCESSFUL, $authcode);
  }

  public function testDuplicateUserShouldFail() {
    $this->cleanUpDatabase();
    $this->usr_gw->method('existsBy')->willReturn(true);
    $this->usr->method('create')
      ->willReturn(true);
    $authcode = $this->auth->createUser("jimmy@gmail.com", "passwordABC");
    $this->assertEquals(Auth::USER_EXISTS, $authcode);
  }

  public function tearDownOld() {
    $this->cleanUpDatabase();
    $this->pdo->exec("INSERT INTO `users` (email,password,user_salt,is_active,is_admin) VALUES ('jimmylospelunking@gmail.com','0c8c69bf9b40caf931c77925ad274a86fed6382ab210849ef26c80cdc87d75da1d5654a4bfa8836f55596b072c523f033de2113cdc09184805134bb2d1c7233f','A{iaD%EtjTt4Ppd74YSCB6Bs4m!euRWB6yKNAFkJ52jG$<xJa*',1,0)");
    // keep this one login for testing purposes:
    //   jimmylospelunking@gmail / spelunking 
  }

  private function cleanUpDatabase() {
    $this->pdo->exec("DELETE FROM `users`"); 
    $this->pdo->exec("DELETE FROM `user_sessions`");
  }

  private function getUsersFromDatabase() {
    $result = $this->pdo->query("SELECT * FROM `users`");
    return $result->fetch(PDO::FETCH_ASSOC);
  }
}
/* @runTestsInSeparateProcesses
*/