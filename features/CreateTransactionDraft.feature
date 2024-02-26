Feature: create a transaction draft

    Scenario: create a transaction draft

        Given There is a api, a file "tests/examples/lorem.PDF" and a user but no marks
        When there is a demand to create a transaction draft
        Then the transaction is created and is returned
