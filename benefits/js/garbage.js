/*
console.log("Earned amount: " + earning + " --- Numbers below this show LOSSES");
console.log(ssiScale(earning, 146) + " ssi");
console.log(ssdiScale(earning, 624) + " ssdi");
console.log(snapScale(earning, 170, true, 250) + " snap");
console.log(sec8Scale(earning, 408) + " sec 8");
console.log(energyScale(earning, 30) + " lieap");
console.log(federalTaxScale(deductedEarning) + " federal tax");
console.log(medicareTaxScale(deductedEarning, selfEmployed) + " medicare tax");
console.log(socialSecurityTaxScale(deductedEarning, selfEmployed) + " SS tax");
console.log(stateTaxScale(earning, stateTaxRate) + " state tax");

var ssiScaleResult = ssiScale(earning, 146);
var ssdiScaleResult = ssdiScale(earning, 624);
var snapScaleResult = snapScale(earning, 170, true, 250);
var sec8ScaleResult = sec8Scale(earning, 408);
var energyScaleResult = energyScale(earning, 30);
var federalTaxScaleResult = federalTaxScale(earning);
var medicareTaxScaleResult = medicareTaxScale(earning, selfEmployed);
var socialSecurityTaxScaleResult = socialSecurityTaxScale(earning, selfEmployed);
var stateTaxScaleResult = stateTaxScale(earning, stateTaxRate);
*/

/*
console.log(combinedLoss(ssiScaleResult, ssdiScaleResult, snapScaleResult, sec8ScaleResult, energyScaleResult,
    federalTaxScaleResult, medicareTaxScaleResult, socialSecurityTaxScaleResult, stateTaxScaleResult));
*/



/*

            barChartData: function () {
    return {
        labels: ['Income 100', 'Income 250', 'Income 500', 'Income 1,000', 'Income 1,175',
            'Income 1,200', 'Income 1,500', 'Income 2,000', 'Income 3,000', 'Income 4000'],
        datasets: [
            {
                label: 'Income vs Loss',
                backgroundColor: [this.steppedColors[1], this.steppedColors[2],
                    this.steppedColors[3], this.steppedColors[4], this.steppedColors[5],
                    this.steppedColors[6], this.steppedColors[7], this.steppedColors[8],
                    this.steppedColors[9], this.steppedColors[10]],
                data: [this.steppedResults[1], this.steppedResults[2],
                    this.steppedResults[3], this.steppedResults[4], this.steppedResults[5],
                    this.steppedResults[6], this.steppedResults[7], this.steppedResults[8],
                    this.steppedResults[9], this.steppedResults[10]]
            }
        ]
    }
},

 */
*
*
* /*
            combinedBenefits: function() {
                this.benefits = parseFloat(this.ssiScale);
                this.benefits += parseFloat(this.ssdiScale);
                this.benefits += parseFloat(this.snapScale);
                this.benefits += parseFloat(this.sec8Scale);
                this.benefits += parseFloat(this.energyScale);
                console.log(this.benefits.toFixed(2));
                return this.benefits.toFixed(2);
            },
*/



/*combinedBenefits: function() {
                this.benefits = parseFloat(this.ssiAmount);
                this.benefits += parseFloat(this.ssdiAmount);
                this.benefits += parseFloat(this.snapAmount);
                this.benefits += parseFloat(this.sec8Amount);
                this.benefits += parseFloat(this.energyAmount);
                this.benefits = this.benefits - this.combinedLoss;

                console.log(this.benefits.toFixed(2));


            },*/





combinedBenefitLoss: function () {
    this.differenceBenefits = combinedLoss(this.ssiScale, this.ssdiScale, this.snapScale,
        this.sec8Scale, this.energyScale).toFixed(2);
    this.differenceBenefits = -this.differenceBenefits + +this.earning;
    this.differenceBenefits = this.differenceBenefits.toFixed(2);
    if (this.difference >= 0){
        this.differenceColor = 'differencePositive';
    } else {
        this.differenceColor = 'differenceNegative';
    }
    // For general usage
    return combinedLoss(this.ssiScale, this.ssdiScale, this.snapScale, this.sec8Scale, this.energyScale,
        this.federalTaxScale, this.medicareTaxScale, this.socialSecurityTaxScale, this.stateTaxScale
    ).toFixed(2);//TODO: This isn't used, yet.
},



//          steppedColors:[],
/*ssiScaleData: 0,
ssdiScaleData: 0,
snapScaleData: 0,
sec8ScaleData: 0,
energyScaleData: 0,
benefits: 0*/





-------------

    // OLD CALCULATIONS.JS


var stateTaxRate = 0;
var selfEmployed = true;
var earning = 1175;
var deductedEarning = 190;

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

var fedBracket = [793.75, 3225, 6875, 13125, 16666, 41666.67, 41666.68];
var fedPercent = [10, 12, 22, 24, 32, 35, 37];

function federalTaxScale(deductedEarning){

    switch(true) {
        case (deductedEarning <= 777.08):
            return (deductedEarning / 10);
        case (deductedEarning <= 3158.75):
            var remainderFromPreviousTaxBrackets = deductedEarning - 777.08;
            var totalExpense = (remainderFromPreviousTaxBrackets * .15);
            totalExpense = totalExpense + 77.7
            return totalExpense;
        case (deductedEarning <= 7658.33):
            var remainderFromPreviousTaxBrackets = deductedEarning - 3158.75; // This final number is the previous case's value in the <= section
            var totalExpense = (remainderFromPreviousTaxBrackets * .25) + 539.85; // This final number is the calculation at max value from the previous sectton (.01 less than the current case's total value)
            return totalExpense;
        case (deductedEarning <= 15970.83):
            var remainderFromPreviousTaxBrackets = deductedEarning - 7658.33;
            var totalExpense = (remainderFromPreviousTaxBrackets * .28) + 1664.75;
            return totalExpense;
        case (deductedEarning <= 34725):
            var remainderFromPreviousTaxBrackets = deductedEarning - 15970.83;
            var totalExpense = (remainderFromPreviousTaxBrackets * .33) + 3992.25;
            return totalExpense;
        case (deductedEarning <= 34866.67):
            var remainderFromPreviousTaxBrackets = deductedEarning - 34725;
            var totalExpense = (remainderFromPreviousTaxBrackets * .35) + 3992.25;
            return totalExpense;
        case (deductedEarning > 34866.67):
            var remainderFromPreviousTaxBrackets = deductedEarning - 34866.67;
            var totalExpense = (remainderFromPreviousTaxBrackets * .396) + 4041.83;
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