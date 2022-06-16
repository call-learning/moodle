@mod @mod_bigbluebuttonbn
Feature: Guest access allows external users to connect to a meeting

  Background:
    Given a BigBlueButton mock server is configured
    And I enable "bigbluebuttonbn" "mod" plugin
    And the following "courses" exist:
      | fullname      | shortname | category |
      | Test Course 1 | C1        | 0        |
    And the following "activities" exist:
      | activity        | name           | intro                           | course | idnumber         | type | recordings_imported |
      | bigbluebuttonbn | RoomRecordings | Test Room Recording description | C1     | bigbluebuttonbn1 | 0    | 0                   |

  @javascript
  Scenario: I need to enable guest access to see the instance parameters
    Given the following config values are set as admin:
      | bigbluebuttonbn_guestaccess_enabled | 1 |
    When I am on the "RoomRecordings" "bigbluebuttonbn activity editing" page logged in as "admin"
    Then I should see "Guest access"
    Then I log out
    Given the following config values are set as admin:
      | bigbluebuttonbn_guestaccess_enabled | 0 |
    When I am on the "RoomRecordings" "bigbluebuttonbn activity editing" page logged in as "admin"
    Then I should not see "Guest access"
    Then I log out
