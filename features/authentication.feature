Feature: User Authentication
  In order to access protected resources
  As a user
  I want to be able to authenticate with my credentials

  Background:
    Given an existing user with email "john@doe.com" and password "password"

  Scenario: Successful login
    When I add "Content-Type" header equal to "application/json"
    And I send a POST request to "/api/login" with body:
      """
      {
        "username": "john@doe.com",
        "password": "password"
      }
      """
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "token" should not be null

  Scenario: Unsuccessful login with invalid credentials
    When I add "Content-Type" header equal to "application/json"
    And I send a POST request to "/api/login" with body:
      """
      {
        "username": "john@doe.com",
        "password": "wrongpassword"
      }
      """
    Then the response status code should be 401
    And the response should be in JSON
    And the JSON node "code" should contain "401"
    And the JSON node "message" should contain "Invalid credentials."
