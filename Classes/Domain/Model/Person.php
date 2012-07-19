<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Michel Georgy <michel at 4eyes.ch>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 *
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Tx_X4epersdb_Domain_Model_Person extends Tx_Extbase_DomainObject_AbstractEntity {


    /**
     *
     * @var int
     */
    var $alumni;

    /**
     *
     * @var string
     */
    var $function;

    /**
     *
     * @var int
     */
    var $departments;

    /**
     *
     * @var string
     */
    var $functionSuffix;

    /**
     *
     * @var string
     */
    var $title;

    /**
     *
     * @var string
     */
    var $firstname;

    /**
     *
     * @var string
     */
    var $lastname;

    /**
     *
     * @var string
     */
    var $titleAfter;

    /**
     *
     * @var string
     */
    var $email;

    /**
     *
     * @var string
     */
    var $email2;

    /**
     *
     * @var string
     */
    var $mobilePhone;

    /**
     *
     * @var string
     */
    var $alias;

    /**
     *
     * @var string
     */
    var $image;

    /**
     *
     * @var string
     */
    var $officeAddress;

    /**
     *
     * @var string
     */
    var $officeRoomnumber;

    /**
     *
     * @var int
     */
    var $officeZip;

    /**
     *
     * @var string
     */
    var $officeLocation;

    /**
     *
     * @var string
     */
    var $officeCountry;

    /**
     *
     * @var string
     */
    var $officePhone;

    /**
     *
     * @var string
     */
    var $officePhone2;

    /**
     *
     * @var string
     */
    var $officeFax;

    /**
     *
     * @var string
     */
    var $address;

    /**
     *
     * @var int
     */
    var $zip;

    /**
     *
     * @var string
     */
    var $city;

    /**
     *
     * @var string
     */
    var $country;

    /**
     *
     * @var string
     */
    var $phone;

    /**
     *
     * @var string
     */
    var $phone2;

    /**
     *
     * @var string
     */
    var $mobile;

    /**
     *
     * @var string
     */
    var $fax;

    /**
     *
     * @var string
     */
    var $url;

    /**
     *
     * @var string
     */
    var $beuser;

    /**
     *
     * @var string
     */
    var $personalPage;

    /**
     *
     * @var string
     */
    var $resumePage;

    /**
     *
     * @var string
     */
    var $coursePage;

    /**
     *
     * @var string
     */
    var $researchPage;

    /**
     *
     * @var string
     */
    var $officeMobilePhone;

    /**
     *
     * @var string
     */
    var $profile;

    /**
     *
     * @var string
     */
    var $addInfo;

    /**
     *
     * @var string
     */
    var $news;

    /**
     *
     * @var string
     */
    var $research;

    /**
     *
     * @var string
     */
    var $membership;

    /**
     *
     * @var int
     */
    var $publadmin;

    /**
     *
     * @var int
     */
    var $qualiadmin;

    /**
     *
     * @var int
     */
    var $showpublics;

    /**
     *
     * @var int
     */
    var $showpublicsinmenu;

    /**
     *
     * @var int
     */
    var $showisislink;

    /**
     *
     * @var string
     */
    var $isisid;

    /**
     *
     * @var string
     */
    var $password;

    /**
     *
     * @var string
     */
    var $feuserId;

    /**
     *
     * @var string
     */
    var $username;

    /**
     *
     * @var string
     */
    var $lectureLink;

    /**
     *
     * @var string
     */
    var $floor;

    /**
     *
     * @var string
     */
    var $room;

    /**
     *
     * @var int
     */
    var $buildings;

    /**
     *
     * @var int
     */
    var $institutes;

    /**
     *
     * @var string
     */
    var $feGroups;

    /**
     *
     * @var int
     */
    var $dni;

    /**
     *
     * @var int
     */
    var $mcssId;

    /**
     *
     * @var int
     */
    var $mainEntry;

    /**
     *
     * @var string
     */
    var $company;

    /**
     *
     * @var int
     */
    var $staticInfoCountry;

    /**
     *
     * @var string
     */
    var $txX4emutationDepartment;

    /**
     *
     * @var string
     */
    var $txX4emutationAffiliation;

    /**
     *
     * @var string
     */
    var $txX4emutationSpeciality;

    /**
     *
     * @var string
     */
    var $externalId;

    /**
     *
     * @var string
     */
    var $rssUrl;

    /**
     *
     * @var string
     */
    var $cruserId;

    /**
     *
     * @return int
     */
    public function getAlumni() {
        return $this->alumni;
    }

    /**
     *
     * @param int $alumni
     */
    public function setAlumni($alumni) {
        $this->alumni = $alumni;
    }

    /**
     *
     * @return string
     */
    public function getFunction() {
        return $this->function;
    }

    /**
     * @param string $function
     */
    public function setFunction($function) {
        $this->function = $function;
    }

    /**
     *
     * @return int
     */
    public function getDepartments() {
        return $this->departments;
    }

    /**
     *
     * @param int $departments
     */
    public function setDepartments($departments) {
        $this->departments = $departments;
    }

    /**
     *
     * @return string
     */
    public function getFunctionSuffix() {
        return $this->functionSuffix;
    }

    /**
     *
     * @param string $functionSuffix
     */
    public function setFunctionSuffix($functionSuffix) {
        $this->functionSuffix = $functionSuffix;
    }

    /**
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     *
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     *
     * @return string
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     *
     * @param string $firstname
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;
    }

    /**
     *
     * @return string
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     *
     * @param string $lastname
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    /**
     *
     * @return string
     */
    public function getTitleAfter() {
        return $this->titleAfter;
    }

    /**
     *
     * @param string $titleAfter
     */
    public function setTitleAfter($titleAfter) {
        $this->titleAfter = $titleAfter;
    }

    /**
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     *
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     *
     * @return string
     */
    public function getEmail2() {
        return $this->email2;
    }

    /**
     *
     * @param string $email2
     */
    public function setEmail2($email2) {
        $this->email2 = $email2;
    }

    /**
     *
     * @return string
     */
    public function getMobilePhone() {
        return $this->mobilePhone;
    }

    /**
     *
     * @param string $mobilePhone
     */
    public function setMobilePhone($mobilePhone) {
        $this->mobilePhone = $mobilePhone;
    }

    /**
     *
     * @return string
     */
    public function getAlias() {
        return $this->alias;
    }

    /**
     *
     * @param string $alias
     */
    public function setAlias($alias) {
        $this->alias = $alias;
    }

    /**
     *
     * @return string
     */
    public function getImage() {
        return $this->image;
    }

    /**
     *
     * @param string $image
     */
    public function setImage($image) {
        $this->image = $image;
    }

    /**
     *
     * @return string
     */
    public function getOfficeAddress() {
        return $this->officeAddress;
    }

    /**
     *
     * @param string $officeAddress
     */
    public function setOfficeAddress($officeAddress) {
        $this->officeAddress = $officeAddress;
    }

    /**
     *
     * @return string
     */
    public function getOfficeRoomnumber() {
        return $this->officeRoomnumber;
    }

    /**
     *
     * @param string $officeRoomnumber
     */
    public function setOfficeRoomnumber($officeRoomnumber) {
        $this->officeRoomnumber = $officeRoomnumber;
    }

    /**
     *
     * @return int
     */
    public function getOfficeZip() {
        return $this->officeZip;
    }

    /**
     *
     * @param int $officeZip
     */
    public function setOfficeZip($officeZip) {
        $this->officeZip = $officeZip;
    }

    /**
     *
     * @return string
     */
    public function getOfficeLocation() {
        return $this->officeLocation;
    }

    /**
     *
     * @param string $officeLocation
     */
    public function setOfficeLocation($officeLocation) {
        $this->officeLocation = $officeLocation;
    }

    /**
     *
     * @return string
     */
    public function getOfficeCountry() {
        return $this->officeCountry;
    }

    /**
     *
     * @param string $officeCountry
     */
    public function setOfficeCountry($officeCountry) {
        $this->officeCountry = $officeCountry;
    }

    /**
     *
     * @return string
     */
    public function getOfficePhone() {
        return $this->officePhone;
    }

    /**
     *
     * @param string $officePhone
     */
    public function setOfficePhone($officePhone) {
        $this->officePhone = $officePhone;
    }

    /**
     *
     * @return string
     */
    public function getOfficePhone2() {
        return $this->officePhone2;
    }

    /**
     *
     * @param string $officePhone2
     */
    public function setOfficePhone2($officePhone2) {
        $this->officePhone2 = $officePhone2;
    }

    /**
     *
     * @return string
     */
    public function getOfficeFax() {
        return $this->officeFax;
    }

    /**
     *
     * @param string $officeFax
     */
    public function setOfficeFax($officeFax) {
        $this->officeFax = $officeFax;
    }

    /**
     *
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     *
     * @param string $address
     */
    public function setAddress($address) {
        $this->address = $address;
    }

    /**
     *
     * @return int
     */
    public function getZip() {
        return $this->zip;
    }

    /**
     *
     * @param int $zip
     */
    public function setZip($zip) {
        $this->zip = $zip;
    }

    /**
     *
     * @return string
     */
    public function getCity() {
        return $this->city;
    }

    /**
     *
     * @param string $city
     */
    public function setCity($city) {
        $this->city = $city;
    }

    /**
     *
     * @return string
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     *
     * @param string $country
     */
    public function setCountry($country) {
        $this->country = $country;
    }

    /**
     *
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     *
     * @param string $phone
     */
    public function setPhone($phone) {
        $this->phone = $phone;
    }

    /**
     *
     * @return string
     */
    public function getPhone2() {
        return $this->phone2;
    }

    /**
     *
     * @param string $phone2
     */
    public function setPhone2($phone2) {
        $this->phone2 = $phone2;
    }

    /**
     *
     * @return string
     */
    public function getMobile() {
        return $this->mobile;
    }

    /**
     *
     * @param string $mobile
     */
    public function setMobile($mobile) {
        $this->mobile = $mobile;
    }

    /**
     *
     * @return string
     */
    public function getFax() {
        return $this->fax;
    }

    /**
     *
     * @param string $fax
     */
    public function setFax($fax) {
        $this->fax = $fax;
    }

    /**
     *
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     *
     * @param string $url
     */
    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     *
     * @return string
     */
    public function getBeuser() {
        return $this->beuser;
    }

    /**
     *
     * @param string $beuser
     */
    public function setBeuser($beuser) {
        $this->beuser = $beuser;
    }

    /**
     *
     * @return string
     */
    public function getPersonalPage() {
        return $this->personalPage;
    }

    /**
     *
     * @param string $personalPage
     */
    public function setPersonalPage($personalPage) {
        $this->personalPage = $personalPage;
    }

    /**
     *
     * @return string
     */
    public function getResumePage() {
        return $this->resumePage;
    }

    /**
     *
     * @param string $resumePage
     */
    public function setResumePage($resumePage) {
        $this->resumePage = $resumePage;
    }

    /**
     *
     * @return string
     */
    public function getCoursePage() {
        return $this->coursePage;
    }

    /**
     *
     * @param string $coursePage
     */
    public function setCoursePage($coursePage) {
        $this->coursePage = $coursePage;
    }

    /**
     *
     * @return string
     */
    public function getResearchPage() {
        return $this->researchPage;
    }

    /**
     *
     * @param string $researchPage
     */
    public function setResearchPage($researchPage) {
        $this->researchPage = $researchPage;
    }

    /**
     *
     * @return string
     */
    public function getOfficeMobilePhone() {
        return $this->officeMobilePhone;
    }

    /**
     *
     * @param string $officeMobilePhone
     */
    public function setOfficeMobilePhone($officeMobilePhone) {
        $this->officeMobilePhone = $officeMobilePhone;
    }

    /**
     *
     * @return string
     */
    public function getProfile() {
        return $this->profile;
    }

    /**
     *
     * @param string $profile
     */
    public function setProfile($profile) {
        $this->profile = $profile;
    }

    /**
     *
     * @return string
     */
    public function getAddInfo() {
        return $this->addInfo;
    }

    /**
     *
     * @param string $addInfo
     */
    public function setAddInfo($addInfo) {
        $this->addInfo = $addInfo;
    }

    /**
     *
     * @return string
     */
    public function getNews() {
        return $this->news;
    }

    /**
     *
     * @param string $news
     */
    public function setNews($news) {
        $this->news = $news;
    }

    /**
     *
     * @return string
     */
    public function getResearch() {
        return $this->research;
    }

    /**
     *
     * @param string $research
     */
    public function setResearch($research) {
        $this->research = $research;
    }

    /**
     *
     * @return string
     */
    public function getMembership() {
        return $this->membership;
    }

    /**
     *
     * @param string $membership
     */
    public function setMembership($membership) {
        $this->membership = $membership;
    }

    /**
     *
     * @return string
     */
    public function getPubladmin() {
        return $this->publadmin;
    }

    /**
     *
     * @param string $publadmin
     */
    public function setPubladmin($publadmin) {
        $this->publadmin = $publadmin;
    }

    /**
     *
     * @return string
     */
    public function getQualiadmin() {
        return $this->qualiadmin;
    }

    /**
     *
     * @param string $qualiadmin
     */
    public function setQualiadmin($qualiadmin) {
        $this->qualiadmin = $qualiadmin;
    }

    /**
     *
     * @return int
     */
    public function getShowpublics() {
        return $this->showpublics;
    }

    /**
     *
     * @param int $showpublics
     */
    public function setShowpublics($showpublics) {
        $this->showpublics = $showpublics;
    }

    /**
     *
     * @return int
     */
    public function getShowpublicsinmenu() {
        return $this->showpublicsinmenu;
    }

    /**
     *
     * @param int $showpublicsinmenu
     */
    public function setShowpublicsinmenu($showpublicsinmenu) {
        $this->showpublicsinmenu = $showpublicsinmenu;
    }

    /**
     *
     * @return int
     */
    public function getShowisislink() {
        return $this->showisislink;
    }

    /**
     *
     * @param int $showisislink
     */
    public function setShowisislink($showisislink) {
        $this->showisislink = $showisislink;
    }

    /**
     *
     * @return string
     */
    public function getIsisid() {
        return $this->isisid;
    }

    /**
     *
     * @param string $isisid
     */
    public function setIsisid($isisid) {
        $this->isisid = $isisid;
    }

    /**
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     *
     * @return string
     */
    public function getFeuserId() {
        return $this->feuserId;
    }

    /**
     *
     * @param string $feuserId
     */
    public function setFeuserId($feuserId) {
        $this->feuserId = $feuserId;
    }

    /**
     *
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     *
     * @param string $username
     */
    public function setUsername($username) {
        $this->username = $username;
    }

    /**
     *
     * @return string
     */
    public function getLectureLink() {
        return $this->lectureLink;
    }

    /**
     *
     * @param string $lectureLink
     */
    public function setLectureLink($lectureLink) {
        $this->lectureLink = $lectureLink;
    }

    /**
     *
     * @return string
     */
    public function getFloor() {
        return $this->floor;
    }

    /**
     *
     * @param string $floor
     */
    public function setFloor($floor) {
        $this->floor = $floor;
    }

    /**
     *
     * @return string
     */
    public function getRoom() {
        return $this->room;
    }

    /**
     *
     * @param string $room
     */
    public function setRoom($room) {
        $this->room = $room;
    }

    /**
     *
     * @return int
     */
    public function getBuildings() {
        return $this->buildings;
    }

    /**
     *
     * @param string $buildings
     */
    public function setBuildings($buildings) {
        $this->buildings = $buildings;
    }

    /**
     *
     * @return string
     */
    public function getInstitutes() {
        return $this->institutes;
    }

    /**
     *
     * @param int $institutes
     */
    public function setInstitutes($institutes) {
        $this->institutes = $institutes;
    }

    /**
     *
     * @return string
     */
    public function getFeGroups() {
        return $this->feGroups;
    }

    /**
     *
     * @param string $feGroups
     */
    public function setFeGroups($feGroups) {
        $this->feGroups = $feGroups;
    }

    /**
     *
     * @return int
     */
    public function getDni() {
        return $this->dni;
    }

    /**
     *
     * @param int $dni
     */
    public function setDni($dni) {
        $this->dni = $dni;
    }

    /**
     *
     * @return int
     */
    public function getMcssId() {
        return $this->mcssId;
    }

    /**
     *
     * @param int $mcssId
     */
    public function setMcssId($mcssId) {
        $this->mcssId = $mcssId;
    }

    /**
     *
     * @return int
     */
    public function getMainEntry() {
        return $this->mainEntry;
    }

    /**
     *
     * @param string $mainEntry
     */
    public function setMainEntry($mainEntry) {
        $this->mainEntry = $mainEntry;
    }

    /**
     *
     * @return string
     */
    public function getCompany() {
        return $this->company;
    }

    /**
     *
     * @param string $company
     */
    public function setCompany($company) {
        $this->company = $company;
    }

    /**
     *
     * @return int
     */
    public function getStaticInfoCountry() {
        return $this->staticInfoCountry;
    }

    /**
     *
     * @param int $staticInfoCountry
     */
    public function setStaticInfoCountry($staticInfoCountry) {
        $this->staticInfoCountry = $staticInfoCountry;
    }

    /**
     *
     * @return string
     */
    public function getTxX4emutationDepartment() {
        return $this->txX4emutationDepartment;
    }

    /**
     *
     * @param string $txX4emutationDepartment
     */
    public function setTxX4emutationDepartment($txX4emutationDepartment) {
        $this->txX4emutationDepartment = $txX4emutationDepartment;
    }

    /**
     *
     * @return string
     */
    public function getTxX4emutationAffiliation() {
        return $this->txX4emutationAffiliation;
    }

    /**
     *
     * @param string $txX4emutationAffiliation
     */
    public function setTxX4emutationAffiliation($txX4emutationAffiliation) {
        $this->txX4emutationAffiliation = $txX4emutationAffiliation;
    }

    /**
     *
     * @return string
     */
    public function getTxX4emutationSpeciality() {
        return $this->txX4emutationSpeciality;
    }

    /**
     *
     * @param string $txX4emutationSpeciality
     */
    public function setTxX4emutationSpeciality($txX4emutationSpeciality) {
        $this->txX4emutationSpeciality = $txX4emutationSpeciality;
    }

    /**
     *
     * @return string
     */
    public function getExternalId() {
        return $this->externalId;
    }

    /**
     *
     * @param string $externalId
     */
    public function setExternalId($externalId) {
        $this->externalId = $externalId;
    }

    /**
     *
     * @return string
     */
    public function getRssUrl() {
        return $this->rssUrl;
    }

    /**
     *
     * @param string $rssUrl
     */
    public function setRssUrl($rssUrl) {
        $this->rssUrl = $rssUrl;
    }

    /**
     *
     * @return string
     */
    public function getCruserId(){
        return $this->cruserId;
    }

    /**
     *
     * @param string $cruserId
     */
    public function setCruserId($cruserId){
        $this->cruserId = $cruserId;
    }
}

?>
