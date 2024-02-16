Feature: Instanciate Api

    Scenario: create an api client instance

        Given There is a client_id "634d74c96825d" and client_secret "sk_example1234"
        When there is a demand to instantiate the Client with these credentials
        Then the Client is instanciated correctly
