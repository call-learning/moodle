@mod @mod_bigbluebuttonbn @javascript
Feature: Bigbluebuttonbn rooms
When a meeting is created, roles for each type of participant can be changed

  Background:  Make sure that a course is created
    Given I enable "bigbluebuttonbn" "mod" plugin
    And the following course exists:
      | name      | Test course |
      | shortname | C1          |
    And the following "users" exist:
      | username | firstname | lastname | email                 |
      | traverst | Terry     | Travers  | t.travers@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | traverst | C1     | student |
    And the following "activities" exist:
      | activity        | name           | intro                           | course | idnumber         | type | recordings_imported |
      | bigbluebuttonbn | RoomRecordings | Test Room Recording description | C1     | bigbluebuttonbn1 | 0    | 0                   |

  Scenario: Add a mod_bigbluebuttonbn instance and set the teacher role as moderator
    Given I am on the "RoomRecordings" "bigbluebuttonbn activity editing" page logged in as "admin"
    And I set the field "bigbluebuttonbn_participant_selection_type" to "Role"
    Then I set the field "bigbluebuttonbn_participant_selection" to "Manager"
    Then I click on "bigbluebuttonbn_participant_selection_add" "button"
    Then I set the field "select-for-role-1" to "Moderator"
    Then I press "Save and display"
    Then I am on the "RoomRecordings" "bigbluebuttonbn activity editing" page logged in as "admin"
    Then "[name=select-for-role-1] option[value=moderator][selected]" "css_element" should exist

  Scenario: Set the role moderator to a new role and then delete the role. The role should not appear anymore but other roles
  should be kept.
    Given the following "roles" exist:
      | shortname | name   | archetype |
      | role1     | Role 1 | student   |
    Given I am on the "RoomRecordings" "bigbluebuttonbn activity editing" page logged in as "admin"
    And I set the field "bigbluebuttonbn_participant_selection_type" to "Role"
    Then I set the field "bigbluebuttonbn_participant_selection" to "Role 1"
    Then I click on "bigbluebuttonbn_participant_selection_add" "button"
    Then I set the field with xpath "//*[contains(@name,'select-for-role')]" to "Moderator"
    And I set the field "bigbluebuttonbn_participant_selection_type" to "Role"
    Then I set the field "bigbluebuttonbn_participant_selection" to "Manager"
    Then I click on "bigbluebuttonbn_participant_selection_add" "button"
    Then I press "Save and display"
    Then I navigate to "Users > Permissions > Define roles" in site administration
    And I click on "Delete" "link" in the "Role 1" "table_row"
    Then I click on "Yes" "button"
    Then I am on the "RoomRecordings" "bigbluebuttonbn activity editing" page logged in as "admin"
    Then I should not see "Role 1" in the "#participant_list_table" "css_element"
    And I should see "Manager" in the "#participant_list_table" "css_element"
