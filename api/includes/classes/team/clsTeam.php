<?php

/*
-----------------------------------------------------------------------------------------------------------
Class: clsTeam.php
Version: 1.0
Release Date:
-----------------------------------------------------------------------------------------------------------
Overview: Class to handle team management
-----------------------------------------------------------------------------------------------------------
History:
19/01/2015      1.0	MJC	Created
-----------------------------------------------------------------------------------------------------------
Uses:

*/

Class Team
{
			private $teamID;
			private $teamTitle;
			private $teamDescription;

				/*----------------------------------------------------------------------------------
				Function:	init
				Overview:	Function to initialize object attributes

				In:      $operation      String          Operation(create,delete,update)
						 $arrTeamInfo     Array           Array of team information


				Out:     true/false      bool
				----------------------------------------------------------------------------------*/
				public function init($operation,$intID=0,$arrTeamInformation=array())
				{

						$objFeedback = new Feedback();

						if(count($arrTeamInformation)<1)
						{

							switch($operation)
							{
								case "returnAllTeams":
									return $this->returnAllTeams();
								break;



								case "returnSingleTeamMembers":
									return $this->returnSingleTeamMembers($intID);
								break;

								case "getProjectUsers":
									return $this->returnProjectUsers($intID);
								break;


								case "returnTeamMembersByTeamID":
									return $this->returnTeamMembersByTeamID($intID);
								break;


								case "returnTeamIDByName":

									return $this->returnTeamIDByName($intID);
								break;


								case "returnTeamMembersByProjectID":
									return $this->returnTeamMembersByProjectID($intID);
								break;
							}
						}

								//$arrTeam = $arrTeamInformation['team'];

								//foreach($arrTeamInformation as $arrIndTeam)
								//{

												$bFormFailed = false;

												(isset($arrTeamInformation['ID'])) ? $this->setTeamID($arrTeamInformation['ID']) : '';

												(isset($arrTeamInformation['teamTitle'])) ? $this->setTeamTitle($arrTeamInformation['teamTitle']) : $bFormFailed = true;
												(isset($arrTeamInformation['teamDescription'])) ? $this->setTeamDescription($arrTeamInformation['teamDescription']) : $bFormFailed = true;


												if($bFormFailed)
												{

														return false;
												}

												switch($operation)
												{

														case 'create':
																		return $this->createTeam();
														break;



														case 'update':
																		return $this->updateTeam();
														break;


														case 'delete':
																		return $this->deleteTeam();
														break;

														default:
																		return false;
														break;


												}

								//}

				}



				public function returnSingleTeamMembers($id)
				{
					$objDatabase = new Database();
					$query = "SELECT employeeteams.employeeID, employees.firstname, employees.lastname, employees.email
								FROM employeeteams
								INNER JOIN employees
								ON employeeteams.employeeID=employees.employeeID
								WHERE employeeteams.teamID = '".$id."'";
					$dbSet = $objDatabase->result($query);


					$arrProjectTeamMembers = array();
					if($objDatabase->exists($dbSet))
					{
						while($row = mysqli_fetch_array($dbSet, MYSQL_ASSOC))
						{

		                	array_push($arrProjectTeamMembers, array("employeeID" => $row['employeeID'], "firstname" => $row['firstname'], "lastname" => $row['lastname'], "email" => $row['email']));
						}

		                return $arrProjectTeamMembers;
					}

					return false;
				}




				public function returnTeamMembersByProjectID($id)
				{
					$objDatabase = new Database();
					$query = "SELECT projectteam.projectID, projectteam.teamID, employeeteams.teamID, employeeteams.employeeID, employees.firstname, employees.lastname, employees.email
					FROM projectteam
					INNER JOIN employeeteams
					ON employeeteams.teamID = projectteam.teamID
					INNER JOIN employees
					ON employees.employeeID = employeeteams.employeeID
					WHERE projectteam.projectID = '".$id."'";
					$dbSet = $objDatabase->result($query);


					$arrProjectTeamMembers = array();
					if($objDatabase->exists($dbSet))
					{
						while($row = mysqli_fetch_array($dbSet, MYSQL_ASSOC))
						{

		                	array_push($arrProjectTeamMembers, array("employeeID" => $row['employeeID'], "firstname" => $row['firstname'], "lastname" => $row['lastname'], "email" => $row['email']));
						}

		                return $arrProjectTeamMembers;
					}

					return false;
				}




				public function returnAllTeams()
				{
					$objDatabase = new Database();
					return $objDatabase->returnAllRows("teams");
				}

				public function returnTeamIDByName($strTeamName)
				{
					$objDatabase = new Database();
					return $objDatabase->returnSingleData("teamID", "teams", "teamTitle", $strTeamName);
				}


				/*----------------------------------------------------------------------------------
				Function:	createTeam
				Overview:	Function to create a team

				In:


				Out:     true/false      bool
				----------------------------------------------------------------------------------*/
				public function createTeam()
				{
								$arrFields = array("teamtitle","teamdescription");
								$arrValues = array($this->getTeamTitle(),$this->getTeamDescription());

								$objDatabase = new Database();
								$objFeedback = new Feedback();

								if($objDatabase->insert('teams',$arrFields,$arrValues))
								{
										return true;
								}
								return false;

				}


				/*----------------------------------------------------------------------------------
				Function:	updateUser
				Overview:	Function to update attributes of a user

				In:


				Out:     true/false      bool
				----------------------------------------------------------------------------------*/
				public function updateUser()
				{
								$objDatabase = new Database();

								$bUpdateFailed = false;

								($strError = $objDatabase->update("bugs","bugTitle",$this->getBugTitle(),"bugID",$this->getBugID())) ? '' : $bUpdateFailed = true;
								($objDatabase->update("bugs","bugDescription",$this->getBugDescription(),"bugID",$this->getBugID())) ? '' : $bUpdateFailed = true;
								($objDatabase->update("bugs","bugLine",$this->getBugLine(),"bugID",$this->getBugID())) ? '' : $bUpdateFailed = true;
								($objDatabase->update("bugs","bugReportedBy",$this->getBugReportedBy(),"bugID",$this->getBugID())) ? '' : $bUpdateFailed = true;
								($objDatabase->update("bugs","bugFixed",$this->getBugFixed(),"bugID",$this->getBugID())) ? '' : $bUpdateFailed = true;
								($objDatabase->update("bugs","bugDeleted",$this->getBugDeleted(),"bugID",$this->getBugID())) ? '' : $bUpdateFailed = true;


								$objFeedback = new Feedback();

								if($bUpdateFailed)
								{
												$error = $objDatabase->getErrors();
												return $objFeedback->setFeedback($error);
								}
								return $objFeedback->setFeedback("Operation Successful");
				}


				/*----------------------------------------------------------------------------------
				Function:	deleteBug
				Overview:	Function to delete a user from the system

				In:


				Out:     true/false      bool
				----------------------------------------------------------------------------------*/
				public function deleteUser()
				{
								$objDatabase = new Database();

								if($objDatabase->update("bugs","bugDeleted",1,"bugID",$this->getBugID()))
								{
												return $objFeedback->setFeedback("Operation Successful");
								}
								return $objFeedback->setFeedback("Operation Failed");
				}





				/*----------------------------------------------------------------------------------
				Function:	getTeam
				Overview:	Function to return team attribute from the database

				In:      $intTeamID       Integer         Team ID


				Out:     $arrRow         Array           Team information
				----------------------------------------------------------------------------------*/
				public function getTeam($intTeamID)
				{
								$objDatabase = new Database();

								$arrRow = $objDatabase->returnRow('teams','teamID',$intTeamID);

								if(!$arrRow)
								{
										return $objDatabase->getErrors();
								}
								return $arrRow;
				}





				/*----------------------------------------------------------------------------------
				Function:	setTeamID
				Overview:	Function to set the team ID attribute

				In:


				Out:     true/false      bool
				----------------------------------------------------------------------------------*/
				public function setTeamID($intTeamID)
				{
								return $this->teamID = $intTeamID;
				}

				/*----------------------------------------------------------------------------------
				Function:	getTeamID
				Overview:	Function to return the team ID attribute

				In:


				Out:     true/false      bool
				----------------------------------------------------------------------------------*/
				public function getTeamID()
				{
								if(isset($this->teamID))
								{
												return $this->teamID;
								}
				}

				/*----------------------------------------------------------------------------------
				Function:	setTeamTitle
				Overview:	Function to set the team title
				In:


				Out:     true/false      bool
				----------------------------------------------------------------------------------*/
				public function setTeamTitle($strTeamTitle)
				{
								return $this->teamTitle = $strTeamTitle;
				}

				/*----------------------------------------------------------------------------------
				Function:	getTeamTitle
				Overview:	Function to return the team title

				In:


				Out:     true/false      bool
				----------------------------------------------------------------------------------*/
				public function getTeamTitle()
				{
								if(isset($this->teamTitle))
								{
												return $this->teamTitle;
								}
				}


				/*----------------------------------------------------------------------------------
				Function:	setTeamDescription
				Overview:	Function to set the team description

				In:


				Out:     true/false      bool
				----------------------------------------------------------------------------------*/
				public function setTeamDescription($strTeamDescription)
				{
								return $this->teamDescription = $strTeamDescription;
				}

				/*----------------------------------------------------------------------------------
				Function:	getTeamDescription
				Overview:	Function to get the team description

				In:


				Out:     true/false      bool
				----------------------------------------------------------------------------------*/
				public function getTeamDescription()
				{
								if(isset($this->teamDescription))
								{
												return $this->teamDescription;
								}
				}

}

?>