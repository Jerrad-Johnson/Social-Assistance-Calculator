
<script>
var stateTaxRate = 0;
var selfEmployed = true;
var earning = 1600;


if (earning > 1000) {
    var deductedEarning = (earning - 1000);
} else {
    var deductedEarning = 0;
}

/* TODO: Note medical expenses. Note that SSA will go by profit before
deduction, unless you have a tax return
// TODO: Add universal function; takes amount, cutoff, percentage lost, and buffer (amount earned
before any loss begins).
// TODO: Add Optional box for how much money you currently have left each month after expenses;
graph with that.
// TODO: Make deduction optional
*/

// SSI allows you to earn an unlimited amount, but takes half of everything beyond the first $20, up to the total SSI
// amount
function ssiScale(earning, ssiAmount){
    if (earning > 20) {
        var ssiLoss = (earning - 20) /2;
        if (ssiAmount >= ssiLoss){
            return ssiLoss;
        } else {
            return ssiAmount; // The loss is equal to the total assistance
        }
        return ssiLoss;
    } else {
        return 0;
    }
}

// SSDI allows you to earn up to 1180 per month, and lose nothing. At 1180, you lose all.
function ssdiScale(earning, ssiAmount){
    var ssiCutOff = 1180;
    if (earning >= ssiCutOff){
        return ssiAmount;
    } else {
        return 0;
    }
}

// Food assistance takes 1/3 of your income up to the amount, or all of it once you reach a certain amount - depending
// on your classification.
function snapScale(earning, snapAmount, snapDisabled, snapCutOff){
    var snapLoss = earning / 3;
    if (snapDisabled == true) {
        if (snapAmount > earning /3){
            return snapLoss;
        } else {
            return snapAmount; // The loss is equal to the total assistance
        }
        return snapLoss
    } else { // Not disabled
        if (snapCutOff > earning) {
            return 0;
        } else {
            return snapAmount;
        }
    }
}

// Section 8 takes 1/3 of your income, up to the amount. (Any change when disabled?)
function sec8Scale(earning, sec8Amount){
    var sec8Loss = (earning /3);
    if (sec8Loss <= sec8Amount){
        return sec8Loss;
    } else {
        return sec8Amount;
    }
}

// LIEAP takes x of your income, up to the amount. (Any change when disabled?)
function energyScale(earning, energyAmount){
    if (earning >= 1485){
        return energyAmount;
    } else {
        return 0;
    }
}

// Federal Income Tax ... change Earning to Deducted Earning

var fedBracket = [0, 793.75, 3225, 6875, 13125, 16666, 41666.67];
const fedPercent = [10, 12, 22, 24, 32, 35, 37];
var bracketSpread = [];
var trueBracketSpread = []; // For finding distance between brackets
var fedOwedFromPreviousBracket = [];
var fedOwedFromPreviousBrackets = []; // For finding how much is owned from all previous brackets combined
var placeholder = 0;
//console.log(fedBracket[1]);
//console.log(deductedEarning);



fedTaxBracketArray();

function fedTaxBracketArray(){
    for (i = 0; i < 6; i++) {
        var index2 = i;
        index2 = ++ index2;
        bracketSpread = fedBracket[i] - fedBracket[index2];
        trueBracketSpread[i] = Math.abs(bracketSpread);
        bracketSpread = Math.abs(bracketSpread);
        bracketSpread = (bracketSpread / 100) * fedPercent[i];
        bracketSpread = Math.abs(bracketSpread);
        fedOwedFromPreviousBracket.push(bracketSpread);
        fedOwedFromPreviousBrackets[i] = fedOwedFromPreviousBracket.reduce((acc, val) => {
            return acc + val;
        });
    }
}

federalTaxScale(deductedEarning);

function federalTaxScale(deductedEarning) {
    switch (true) {
        case (deductedEarning <= fedBracket[1]):
            return (deductedEarning / 100) * fedPercent[0];
        case (deductedEarning <= fedBracket[2]):
            var remainderFromPreviousTaxBrackets = deductedEarning - fedBracket[1];
            var totalExpense = (remainderFromPreviousTaxBrackets * fedPercent[1] / 100) + fedOwedFromPreviousBrackets[0];
            return totalExpense;
        case (deductedEarning <= fedBracket[3]):
            var remainderFromPreviousTaxBrackets = deductedEarning - fedBracket[2];
            var totalExpense = (remainderFromPreviousTaxBrackets * fedPercent[2] / 100) + fedOwedFromPreviousBrackets[1];
            return totalExpense;
        case (deductedEarning <= fedBracket[4]):
            var remainderFromPreviousTaxBrackets = deductedEarning - fedBracket[3];
            var totalExpense = (remainderFromPreviousTaxBrackets * fedPercent[3] / 100) + fedOwedFromPreviousBrackets[2];
            return totalExpense;
        case (deductedEarning <= fedBracket[5]):
            var remainderFromPreviousTaxBrackets = deductedEarning - fedBracket[4];
            var totalExpense = (remainderFromPreviousTaxBrackets * fedPercent[4] / 100) + fedOwedFromPreviousBrackets[3];
            return totalExpense;
        case (deductedEarning <= fedBracket[6]):
            var remainderFromPreviousTaxBrackets = deductedEarning - fedBracket[5];
            var totalExpense = (remainderFromPreviousTaxBrackets * fedPercent[5] / 100) + fedOwedFromPreviousBrackets[4];
            return totalExpense;
        case (deductedEarning > fedBracket[6]):
            var remainderFromPreviousTaxBrackets = deductedEarning - fedBracket[6];
            var totalExpense = (remainderFromPreviousTaxBrackets * fedPercent[6] / 100) + fedOwedFromPreviousBrackets[5];
            return totalExpense;
    }
}


// Medical Benefits

// Medicare tax. Doubled if self-employed.
function medicareTaxScale(deductedEarning, selfEmployed){
    if (selfEmployed == true){
        return deductedEarning * .029;
    } else {
        return deductedEarning * .0145;
    }
}

// Social Security Tax. Doubled if self-employed. Limited to 10,700 deductedEarning.
function socialSecurityTaxScale(deductedEarning, selfEmployed){
    if (selfEmployed == true){
        if (deductedEarning >= 10700){
            return 10700 * (12.4 / 100);
        }
        return deductedEarning * (12.4 / 100);
    } else {
        if (deductedEarning >= 10700){
            return 10700 * (6.2 / 100);
        }
        return deductedEarning * (6.2 / 100);
    }
}

// State Income Tax, if present
function stateTaxScale(earning, stateTaxRate){
    if (stateTaxRate <= 0){
        return 0;
    } else {
        return earning * (stateTaxRate / 100);
    }
}


function combinedLoss(ssiScaleResult, ssdiScaleResult, snapScaleResult, sec8ScaleResult, energyScaleResult,
                      federalTaxScaleResult, medicareTaxScaleResult, socialSecurityTaxScaleResult,
                      stateTaxScaleResult){

    var combinedLoss = +ssiScaleResult + +ssdiScaleResult + +snapScaleResult + +sec8ScaleResult + +energyScaleResult
        + +federalTaxScaleResult + +medicareTaxScaleResult + +socialSecurityTaxScaleResult +
        +stateTaxScaleResult;
    //console.log(combinedLoss);
    return combinedLoss;
}
</script>