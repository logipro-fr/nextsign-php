Feature: create a transaction

    Scenario: create a transaction

        Given There is a api, a file "tests/examples/lorem.PDF" and a user
        When there is a demand to create a transaction
        Then the transaction is created and its ID is returned
