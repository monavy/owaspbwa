<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */


// Call authorizeTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "authorizeTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "testConf.php";

$_SESSION['WPATH'] = WPATH;

require_once "authorize.php";
require_once ROOT_PATH."/lib/confs/Conf.php";

/**
 * Test class for authorize.
 * Generated by PHPUnit_Util_Skeleton on 2006-11-02 at 10:06:38.
 */
class authorizeTest extends PHPUnit_Framework_TestCase {

	public $authorizeObj = null;
	public $connection = null;
	public $testSubject = array('employeeId' => "012", 'isAdmin' => "Yes");

	/**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("authorizeTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() {
    	$this->authorizeObj = new authorize($this->testSubject['employeeId'], $this->testSubject['isAdmin']);

    	$conf = new Conf();

    	$this->connection = mysql_connect($conf->dbhost.":".$conf->dbport, $conf->dbuser, $conf->dbpass);

        mysql_select_db($conf->dbname);

    	$this->_deleteTestData();

        // Insert job titles
        $this->_runQuery("INSERT INTO hs_hr_job_title(jobtit_code, jobtit_name, jobtit_desc, jobtit_comm, sal_grd_code) " .
                "VALUES('JOB001', 'Manager', 'Manager job title', 'no comments', null)");
        $this->_runQuery("INSERT INTO hs_hr_job_title(jobtit_code, jobtit_name, jobtit_desc, jobtit_comm, sal_grd_code) " .
                "VALUES('JOB002', 'Driver', 'Driver job title', 'no comments', null)");
        $this->_runQuery("INSERT INTO hs_hr_job_title(jobtit_code, jobtit_name, jobtit_desc, jobtit_comm, sal_grd_code) " .
                "VALUES('JOB003', 'Director', 'Director job title', 'no comments', null)");

    	$this->_runQuery("INSERT INTO `hs_hr_employee`(emp_number, employee_id, emp_lastname, emp_firstname, emp_nick_name, coun_code) " .
				"VALUES (11, NULL, 'Arnold', 'Subasinghe', 'Arnold', 'AF')");
		$this->_runQuery("INSERT INTO `hs_hr_employee`(emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name, emp_nick_name) " .
				"VALUES (12, NULL, 'Mohanjith', 'Sudirikku', 'Hannadige', 'MOHA')");

        // employees with job titles
        // Driver
        $this->_runQuery("INSERT INTO hs_hr_employee(emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name, job_title_code, emp_work_email) " .
                    "VALUES(13, '0013', 'Rajasinghe', 'Saman', 'Marlon', 'JOB002', 'aruna@company.com')");
        // Manager
        $this->_runQuery("INSERT INTO hs_hr_employee(emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name, job_title_code, emp_work_email) " .
                    "VALUES(14, '0014', 'Jayasinghe', 'Aruna', 'Shantha', 'JOB001', 'arnold@mydomain.com')");
        // Insert director
        $this->_runQuery("INSERT INTO hs_hr_employee(emp_number, employee_id, emp_lastname, emp_firstname, emp_middle_name, job_title_code, emp_work_email) " .
                    "VALUES(15, '0032', 'Samuel', 'John', 'A', 'JOB003', 'mohanjith@mydomain.com')");

		mysql_query("INSERT INTO `hs_hr_emp_reportto` VALUES ('012', '011', 1);");

        mysql_query("INSERT INTO hs_hr_customer(customer_id, name, description, deleted) " .
        			"VALUES(1, 'Test customer', 'description', 0)");
        mysql_query("INSERT INTO hs_hr_project(project_id, customer_id, name, description, deleted) " .
        			"VALUES(1, 1, 'Test project 1', 'a test proj 1', 0)");
        mysql_query("INSERT INTO hs_hr_project(project_id, customer_id, name, description, deleted) " .
        			"VALUES(2, 1, 'Test project 2', 'a test proj 2', 0)");
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown() {
    	$this->_deleteTestData();
    }

	/**
	 * Deletes test data created during test
	 */
	private function _deleteTestData() {
		mysql_query("TRUNCATE TABLE `hs_hr_project`", $this->connection);
        mysql_query("TRUNCATE TABLE `hs_hr_project_admin`", $this->connection);
		mysql_query("TRUNCATE TABLE `hs_hr_customer`", $this->connection);

    	mysql_query("DELETE FROM `hs_hr_employee` WHERE `emp_number` in (11, 12, 13, 14, 15)", $this->connection);

    	mysql_query("DELETE FROM `hs_hr_emp_reportto` WHERE `erep_sup_emp_number` = '012' AND `erep_sub_emp_number` = '011'", $this->connection);
        $this->_runQuery("TRUNCATE TABLE `hs_hr_job_title`");
	}

	/**
	 * Run given sql query
	 */
	private function _runQuery($sql) {
	    $this->assertTrue(mysql_query($sql), mysql_error());
    }

    public function testIsAdmin() {
    	$authObj = new authorize($this->testSubject['employeeId'], 'No');

    	$res = $authObj->isAdmin();

        $this->assertEquals($res, false, "Non admin an Admin");
    }

    public function testIsAdmin2() {
        $res = $this->authorizeObj->isAdmin();

        $this->assertEquals($res, true, "Admin not an Admin");
    }

    public function testIsSupervisor() {
        $authObj = new authorize("041", 'Yes');

    	$res = $authObj->isSupervisor();

        $this->assertEquals($res, false, "non Supervisor an Supervisor");
    }

    public function testIsSupervisor2() {
    	$this->authorizeObj = new authorize($this->testSubject['employeeId'], $this->testSubject['isAdmin']);
        $res = $this->authorizeObj->isSupervisor();

        $this->assertEquals($res, true, "Supervisor not an Supervisor");
    }

    /**
     * Test case for isManager function
     */
    public function testIsManager() {
        // driver
        $authObj = new authorize('013', 'No');
        $this->assertFalse($authObj->isManager());

        $authObj = new authorize('013', 'Yes');
        $this->assertFalse($authObj->isManager());

        // manager
        $authObj = new authorize('014', 'No');
        $this->assertTrue($authObj->isManager());

        $authObj = new authorize('014', 'Yes');
        $this->assertTrue($authObj->isManager());

        // director
        $authObj = new authorize('015', 'No');
        $this->assertFalse($authObj->isManager());

        $authObj = new authorize('015', 'Yes');
        $this->assertFalse($authObj->isManager());
    }

    /**
     * Test case for isDirector function
     */
    public function testIsDirector() {
        // driver
        $authObj = new authorize('013', 'No');
        $this->assertFalse($authObj->isDirector());

        $authObj = new authorize('013', 'Yes');
        $this->assertFalse($authObj->isDirector());

        // manager
        $authObj = new authorize('014', 'No');
        $this->assertFalse($authObj->isDirector());

        $authObj = new authorize('014', 'Yes');
        $this->assertFalse($authObj->isDirector());

        // director
        $authObj = new authorize('015', 'No');
        $this->assertTrue($authObj->isDirector());

        $authObj = new authorize('015', 'Yes');
        $this->assertTrue($authObj->isDirector());
    }

    public function testIsESS() {
        $authObj = new authorize("", 'Yes');

    	$res = $authObj->isESS();

        $this->assertEquals($res, false, "ESS not an ESS");
    }

    public function testIsESS2() {
        $res = $this->authorizeObj->isESS();

        $this->assertEquals($res, true, "ESS not an ESS");
    }

    public function testIsTheSupervisor() {
    	$res = $this->authorizeObj->isTheSupervisor("051");

    	$this->assertEquals($res, false, "The supervisor of unknown employee");
    }

    public function testIsTheSupervisor2() {
    	$res = $this->authorizeObj->isTheSupervisor("011");

    	$this->assertEquals($res, true, "The supervisor of unknown emplyee");
    }

    public function testFirstRole() {
    	$authObj = new authorize("041", 'No');
    	$roleArr = array($authObj->roleAdmin, $authObj->roleSupervisor);
        $res = $authObj->firstRole($roleArr);

        $this->assertEquals($res, false, "Didn't return the first");
    }

    public function testFirstRole2() {
    	$authObj = new authorize($this->testSubject['employeeId'], 'No');
    	$roleArr = array($authObj->roleAdmin, $authObj->roleSupervisor);
        $res = $authObj->firstRole($roleArr);

        $this->assertEquals($res, $authObj->roleSupervisor, "Didn't return the first");
    }

    public function testFirstRole3() {
    	$authObj = $this->authorizeObj;
    	$roleArr = array($authObj->roleAdmin, $authObj->roleSupervisor);
        $res = $authObj->firstRole($roleArr);

        $this->assertEquals($res, $roleArr[0], "Didn't return the first");
    }

	/**
	 * Test case of isProjectAdmin() method
	 */
    public function testIsProjectAdmin() {
    	$authObj = new authorize("012", 'No');
        $this->assertFalse($authObj->isProjectAdmin(), "Not a project admin");
        $this->assertTrue(mysql_query("INSERT INTO hs_hr_project_admin(emp_number, project_id) " .
        			"VALUES(12, 1)"));

		$authObj = new authorize("012", 'No');
		$this->assertTrue($authObj->isProjectAdmin(), "Project admin not identified.");

    }

	/**
	 * Test case of isProjectAdminOf() method
	 */
    public function testIsProjectAdminOf() {

    	$authObj = new authorize("012", 'No');
    	$this->assertFalse($authObj->isProjectAdminOf(1), "Not a project admin");
        mysql_query("INSERT INTO hs_hr_project_admin(emp_number, project_id) " .
        			"VALUES(12, 1)");

		$authObj = new authorize("012", 'No');
    	$this->assertTrue($authObj->isProjectAdminOf(1), "Employee is an admin of project 1");
    	$this->assertFalse($authObj->isProjectAdminOf(2), "Employee is not an admin of project 2");
    }

}

// Call authorizeTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "authorizeTest::main") {
    authorizeTest::main();
}
?>
