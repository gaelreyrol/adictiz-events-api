Feature: Manage marketing events
  As a user
  I want to create a marketing event with a title, description, start date, end date, and status
  I want to update the details of an event I have created
  I want to view the details of an event I have created
  I want to delete an event I have created
  So that I can plan my campaigns

  Background:
    Given an existing user with email "user@example.com" and password "password"

  Scenario: Successfully create an event
    When I set the Authorization header for the user with email "user@example.com" and password "password"
    And I add "Content-Type" header equal to "application/json"
    And I send a POST request to "/api/events" with body:
      """
      {
        "title": "New Marketing Event",
        "description": "A description for the marketing event.",
        "startDate": "2025-02-01",
        "endDate": "2025-02-28",
        "status": "draft"
      }
      """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON node "id" should be an UUID v4
    And the JSON nodes should contain:
    | title       | New Marketing Event                    |
    | description | A description for the marketing event. |
    | status      | draft                                  |
    | startDate   | 2025-02-01                             |
    | endDate     | 2025-02-28                             |

  Scenario: Successfully update an event
    When I set the Authorization header for the user with email "user@example.com" and password "password"
    And I add "Content-Type" header equal to "application/json"
    And I create an event "5567c703-d4be-4cca-94f7-df578088426a", "Initial title", "Initial description", "2025-01-01", "2025-01-01", "draft"
    And I send a PUT request to "/api/events/5567c703-d4be-4cca-94f7-df578088426a" with body:
      """
      {
        "title": "Updated title",
        "description": "Updated description",
        "startDate": "2025-01-01",
        "endDate": "2025-01-15",
        "status": "published"
      }
      """
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON nodes should contain:
      | id          | 5567c703-d4be-4cca-94f7-df578088426a |
      | title       | Updated title                        |
      | description | Updated description                  |
      | status      | published                            |
      | startDate   | 2025-01-01                           |
      | endDate     | 2025-01-15                           |

  Scenario: Successfully view an event
    When I set the Authorization header for the user with email "user@example.com" and password "password"
    And I create an event "5567c703-d4be-4cca-94f7-df578088426a", "Initial title", "Initial description", "2025-01-01", "2025-01-01", "draft"
    And I send a GET request to "/api/events/5567c703-d4be-4cca-94f7-df578088426a"

    Then the response status code should be 200
    And the response should be in JSON
    And the JSON nodes should contain:
      | id          | 5567c703-d4be-4cca-94f7-df578088426a |
      | title       | Initial title                        |
      | description | Initial description                  |
      | status      | draft                                |
      | startDate   | 2025-01-01                           |
      | endDate     | 2025-01-01                           |

  Scenario: Successfully delete an event
    When I set the Authorization header for the user with email "user@example.com" and password "password"
    And I create an event "5567c703-d4be-4cca-94f7-df578088426a", "Initial title", "Initial description", "2025-01-01", "2025-01-01", "draft"
    And I send a DELETE request to "/api/events/5567c703-d4be-4cca-94f7-df578088426a"

    Then the response status code should be 204
    And the response should be empty
