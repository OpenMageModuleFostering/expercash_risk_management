ExperCashValidation = Class.create();
ExperCashValidation.prototype = {
    initialize: function() {
    },
    /**
     * appends the solvency checkbox to the billing address
     */
    appendSolvencyCheckToBilling: function () {
        $('billing-buttons-container').parentNode.insertBefore($('scoring_agreement'), $('billing-buttons-container'));
    }
};

