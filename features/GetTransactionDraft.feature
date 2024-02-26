Feature: create a transaction draft

    Scenario: create a transaction draft

        Given There is an id "tdf_12345"
        When there is a demand to get the corresponding transaction draft
        Then the transaction draft is returned
