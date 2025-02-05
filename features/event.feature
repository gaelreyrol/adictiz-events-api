Feature: Manage marketing events
  As a user
  I want to create a marketing event with a title, description, start date, end date, and status
  I want to update the details of an event I have created
  I want to view the details of an event I have created
  I want to delete an event I have created
  I want to list the events I have created
  So that I can plan my campaigns

  Background:
    Given an existing user with email "user@example.com" and password "password"
    And I set the Authorization header for the user with email "user@example.com" and password "password"
    And I add "Content-Type" header equal to "application/json"

  Scenario: Successfully create an event
    When I send a POST request to "/api/events" with body:
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
    When I create an event "5567c703-d4be-4cca-94f7-df578088426a", "Initial title", "Initial description", "2025-01-01", "2025-01-01", "draft"
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
    When I create an event "5567c703-d4be-4cca-94f7-df578088426a", "Initial title", "Initial description", "2025-01-01", "2025-01-01", "draft"
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
    When I create an event "5567c703-d4be-4cca-94f7-df578088426a", "Initial title", "Initial description", "2025-01-01", "2025-01-01", "draft"
    And I send a DELETE request to "/api/events/5567c703-d4be-4cca-94f7-df578088426a"

    Then the response status code should be 204
    And the response should be empty

  Scenario: Successfully list events
    When I create multiple events:
      | id                                   | title   | description   | startDate  | endDate    | status     |
      | 53dfebee-aebb-46ed-9bfc-22256299947d | title A | description A | 2025-01-01 | 2026-02-01 | draft      |
      | 372bc3fa-956c-47d4-802b-7e22ecf89b52 | title B | description B | 2025-01-02 | 2026-03-01 | published  |
      | 3fd1dc50-9776-4629-a086-e7ed8d6989c4 | title C | description C | 2025-01-03 | 2026-04-01 | archived   |
      | b2683954-068e-4a3a-8353-084c34549638 | title D | description D | 2025-01-04 | 2026-05-01 | draft      |
    And I send a GET request to "/api/events"

    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data" should have 4 elements

  Scenario: Successfully list events with status filter and pagination
    When I create multiple events:
      | id                                   | title   | description   | startDate  | endDate    | status     |
      | 53dfebee-aebb-46ed-9bfc-22256299947d | title A | description A | 2025-01-01 | 2026-02-01 | draft      |
      | 372bc3fa-956c-47d4-802b-7e22ecf89b52 | title B | description B | 2025-01-02 | 2026-03-01 | published  |
      | 3fd1dc50-9776-4629-a086-e7ed8d6989c4 | title C | description C | 2025-01-03 | 2026-04-01 | archived   |
      | b2683954-068e-4a3a-8353-084c34549638 | title D | description D | 2025-01-04 | 2026-05-01 | draft      |
    And I send a GET request to "/api/events" with parameters:
      | key    | value |
      | status | draft |
      | page   | 1     |
      | limit  | 1     |

    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data" should have 1 elements
    And the JSON node "data[0].id" should be equal to "53dfebee-aebb-46ed-9bfc-22256299947d"
