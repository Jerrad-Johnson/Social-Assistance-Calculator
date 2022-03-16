<html>
<head>
    <script src=" js/vue.js "></script>
    <script src=" js/calculations.js "></script>
    <script src=" js/Chart.min.js "></script>
    <script src=" js/vue-chartjs.min.js "></script>
    <script src=" js/innerChartText.js "></script>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/styles.css">

    <title>Benefits Calculator</title>
</head>
<body>

<!-- TODO: Add notes about pell grant, medical coverage ... Calc doesn't account for mixed (self-emp,
other income... That all numbers are monthly
Make responsive
Line stepper; show income/loss at various points
Show total lost
Note 12k std deduction. Note to NOT use this if self-employed.-->

<div id="container">

    <div id="main-left">

        This calculator is designed to show how much you'll gain or lose based on your income, and how much you currently receive in public assistance.
        <br /><br />As you'll see, sometimes earning more means you have <b>less</b> money.
        <br /><br />And remember to account for benefits not mentioned here, such as <b>medical assistance</b> and the Pell Grant.  <br /><br />
        All calculations are designed for monthly values. For example: if you earn 16,000 per year, divide it by 12 (months) and enter 1,333.
        <br /><br /><br />
            <form>
                Disabled<br />
                <input type="radio" v-model="disabled" value="1"> True
                <input type="radio" v-model="disabled" value="0"> False<br />
                Self-employed<br/>
                <input type="radio" v-model="selfEmployed" value="1"> True
                <input type="radio" v-model="selfEmployed" value="0"> False<br />
                <br />
                SSI Income<br/>
                <input type="number" v-model="ssiAmount"> Lost: {{ ssiScale }}<br /> <!--type="number"-->
                SSDI Income<br/>
                <input type="number" v-model="ssdiAmount"> Lost: {{ ssdiScale }}<br />
                SNAP Credit<br/>
                <input type="number" v-model="snapAmount"> Lost: {{ snapScale }}<br />
                SNAP Cutoff (If not disabled)<br/>
                <input type="number" v-model="snapCutOff"><br />
                Section 8 Credit<br/>
                <input type="number" v-model="sec8Amount"> Lost: {{ sec8Scale }}<br />
                LIEAP Credit<br/>
                <input type="number" v-model="energyAmount"> Lost: {{ energyScale }}<br /><br />

                Income<br>
                <input type="number" v-model.number="earning"> <br/>
                Fed. Taxed After Deduction<br/> <!-- TODO: Add option to use standard deduction of 12k -->
                <input type="number" v-model.number="deductedEarning"> Lost: {{ federalTaxScale }}<br />
                State Tax<br/>
                <input type="number" v-model.number="stateTaxPercent"> Lost: {{ stateTaxScale }}<br />

                <br /><br />
                Lost to medicare tax: {{ medicareTaxScale }} <br />
                Lost to social security tax: {{ socialSecurityTaxScale }}<br />
                Earned: <font color="green">{{ earning.toFixed(2) }}</font> --- Lost: <font color="red"> {{ combinedLoss }}</font> <!-- todo: calculate difference -->
                {{ incomeAfterTaxes }} {{ stepper }} <!-- Garbage line, vue requires this -->

            </form>



    </div>

    <div id="main-right">
        <doughnut-chart id="doughnut" v-bind:chart-data="doughnutChartData" ></doughnut-chart><br /><br />

        <center>Difference: <span class="difference" v-bind:class="differenceColor">{{ difference }}</span></center>
        <br /><br />
        The next graphs assumes that if you're self-employed, you're paying yourself all your profit. They also apply the standard deduction (2018) of $12,000. The previous (above) graph does neither of those, and if you select self-employed: <b>treats your deduction as your business expenses.</b>
        <br /><br />
        Left to Right (below) uses pre-defined income amounts.
        <br /><br />The first graph shows the value of your income plus benefits, at different income levels.
        <br /><br />
        <bar-chart v-bind:chart-data="barChartData2"></bar-chart>
        <bar-chart v-bind:chart-data="barChartData"></bar-chart>

    </div>
</div>

<script src=" js/ChartComponents.js "></script>

<script>
    var nVue = new Vue({
        el: '#container',
        data: {
            earning:"1185",
            stateTaxPercent:"0",
            disabled:1,
            selfEmployed:0,
            ssiAmount:"146",
            ssdiAmount:"624",
            snapAmount:"170",
            snapCutOff:"1300",
            sec8Amount:"407",
            energyAmount:"40",
            deductedEarning:"118",
            steps:['100', '250', '500', '1000', '1170', '1190', '1500', '2000', '3000',
                '4000'],
            steppedResults:[],
            steppedResultsLoss:[],
            steppedBenefits:[],
            difference: 0,
            differenceColor: 'differenceGreen',
            incomeAfterTax: '0',
            incomePlusBenefits:[],

        },

        computed: {
            doughnutChartData: function () {
                return {
                    labels: ['Income', 'SSI', 'SSDI', 'SNAP', 'Sec. 8', 'LIEAP', 'Federal Tax', 'State Tax',
                        'Medicare Tax', 'SS. Tax'],
                    datasets: [
                        {
                            label: 'Data Three',
                            backgroundColor: ['green', '#ff0000', '#ee0000', '#dd0000', '#cc0000', '#bb0000',
                                '#aa0000', '#990000', '#880000', '#770000', '#660000'],
                            data: [this.earning, this.ssiScale, this.ssdiScale, this.snapScale, this.sec8Scale,
                                this.energyScale, this.federalTaxScale, this.stateTaxScale,
                                this.medicareTaxScale, this.socialSecurityTaxScale],
                        }
                    ],
                    options: {
                    }
                }
            },

            barChartData: function () {
                return {
                    labels: ['$100', '$250', '$500', '$1,000', '$1,175',
                        '$1,200', '$1,500', '$2,000', '$3,000', '$4000'],
                    datasets: [
                        {
                            label: 'Income',
                            backgroundColor: 'rgba(0, 200, 0, 0.8',
                            data: [100, 250, 500, 1000, 1175, 1200, 1500, 2000, 3000, 4000]
                        }, {
                            label: 'Loss',
                            backgroundColor: 'rgba(255, 96, 96, 0.8',
                            data: [...this.steppedResultsLoss]
                        }, {
                            label: 'Difference',
                            backgroundColor: ['#bbb'],
                            data: [...this.steppedResults],
                            type:'line',
                            borderColor:'black',
                            cubicInterpolationMode: 'default',
                        }
                    ]
                }
            },

            barChartData2: function () {
                return {
                    labels: ['$100', '$250', '$500', '$1,000', '$1,175',
                        '$1,200', '$1,500', '$2,000', '$3,000', '$4000'],
                    datasets: [
                        {
                            label: 'Income plus Benefits',
                            backgroundColor: 'rgba(0, 200, 0, 0.8',
                            data: [...this.incomePlusBenefits]
                        }
                    ]
                }
            },

            ssiScale: function () {
                //console.log(ssiScale(this.earning, this.ssiAmount));
                this.earning = parseFloat(this.earning);
                this.ssiAmount = parseFloat(this.ssiAmount);
                if (this.selfEmployed == 1) {
                    //this.ssiScaleData = ssiScale(this.deductedEarning, this.ssiAmount).toFixed(2);
                    return ssiScale(this.deductedEarning, this.ssiAmount).toFixed(2);
                } else {
                    //this.ssiScaleData = ssiScale(this.earning, this.ssiAmount).toFixed(2);
                    return ssiScale(this.earning, this.ssiAmount).toFixed(2);
                }
            },

            ssdiScale: function () {
                //console.log(ssdiScale(this.earning, this.ssdiAmount));
                this.earning = parseFloat(this.earning);
                this.ssdiAmount = parseFloat(this.ssdiAmount);
                if (this.selfEmployed == 1) {
                    //this.ssdiScaleData = ssdiScale(this.deductedEarning, this.ssdiAmount).toFixed(2);
                    return ssdiScale(this.deductedEarning, this.ssdiAmount).toFixed(2);
                } else {
                    //this.ssdiScaleData = ssdiScale(this.deductedEarning, this.ssdiAmount).toFixed(2);
                    return ssdiScale(this.earning, this.ssdiAmount).toFixed(2);
                }
            },

            snapScale: function () {
                //console.log(snapScale(this.earning, this.snapAmount, this.disabled, this.snapCutOff));
                this.earning = parseFloat(this.earning);
                this.snapAmount = parseFloat(this.snapAmount);
                this.disabled = parseFloat(this.disabled);
                this.snapCutOff = parseFloat(this.snapCutOff);
                if (this.selfEmployed == 1) {
                    //this.snapScaleData = snapScale(this.deductedEarning, this.snapAmount, this.disabled, this.snapCutOff).toFixed(2);
                    return snapScale(this.deductedEarning, this.snapAmount, this.disabled, this.snapCutOff).toFixed(2);
                } else {
                    //this.snapScaleData = snapScale(this.deductedEarning, this.snapAmount, this.disabled, this.snapCutOff).toFixed(2);
                    return snapScale(this.earning, this.snapAmount, this.disabled, this.snapCutOff).toFixed(2);
                }
            },

            sec8Scale: function () {
                //console.log(sec8Scale(this.earning, this.sec8Amount));
                this.earning = parseFloat(this.earning);
                this.sec8Amount = parseFloat(this.sec8Amount);
                if (this.selfEmployed == 1) {
                    //this.sec8ScaleData = sec8Scale(this.deductedEarning, this.sec8Amount).toFixed(2);
                    return sec8Scale(this.deductedEarning, this.sec8Amount).toFixed(2);
                } else {
                    //this.sec8ScaleData = sec8Scale(this.earning, this.sec8Amount).toFixed(2);
                    return sec8Scale(this.earning, this.sec8Amount).toFixed(2);
                }
                // TODO: Do they account for self-emp?
            },

            energyScale: function () {
                //console.log(energyScale(this.earning, this.energyAmount));
                this.earning = parseFloat(this.earning);
                this.energyAmount = parseFloat(this.energyAmount);
                if (this.selfEmployed == 1) {
                    //this.energyScaleData = energyScale(this.deductedEarning, this.energyAmount).toFixed(2);
                    return energyScale(this.deductedEarning, this.energyAmount).toFixed(2);
                } else {
                    //this.energyScaleData = energyScale(this.deductedEarning, this.energyAmount).toFixed(2);
                    return energyScale(this.earning, this.energyAmount).toFixed(2);
                }
            },

            federalTaxScale: function () {
                //console.log(federalTaxScale(this.deductedEarning));
                this.deductedEarning = parseFloat(this.deductedEarning);
                return federalTaxScale(this.deductedEarning).toFixed(2);
            },

            stateTaxScale: function () {
                //console.log(stateTaxScale(this.earning, this.stateTaxPercent));
                this.earning = parseFloat(this.earning);
                this.stateTaxPercent = parseFloat(this.stateTaxPercent);
                return stateTaxScale(this.earning, this.stateTaxPercent).toFixed(2);
            },

            medicareTaxScale: function () {
                //console.log(medicareTaxScale(this.deductedEarning, this.selfEmployed)); //TODO: This isn't used, yet.
                this.deductedEarning = parseFloat(this.deductedEarning);
                this.selfEmployed = parseFloat(this.selfEmployed);
                return medicareTaxScale(this.deductedEarning, this.selfEmployed).toFixed(2); //TODO: This isn't used, yet.
            },

            socialSecurityTaxScale: function () {
                //console.log(socialSecurityTaxScale(this.deductedEarning, this.selfEmployed)); //TODO: This isn't used, yet.
                this.deductedEarning = parseFloat(this.deductedEarning);
                this.selfEmployed = parseFloat(this.selfEmployed);
                return socialSecurityTaxScale(this.deductedEarning, this.selfEmployed).toFixed(2); //TODO: This isn't used, yet.
            },

            combinedLoss: function () {
                // For inner doughnut
                this.difference = combinedLoss(this.ssiScale, this.ssdiScale, this.snapScale, this.sec8Scale, this.energyScale,
                    this.federalTaxScale, this.medicareTaxScale, this.socialSecurityTaxScale, this.stateTaxScale
                ).toFixed(2);
                this.difference = -this.difference + +this.earning;
                this.difference = this.difference.toFixed(2);
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

            incomeAfterTaxes: function(){


                this.incomePlusBenefits = [];
                for (i = 0; i < this.steps.length; i++) {
                    var earned = this.steps[i];
                    if (earned >= 1000) {
                        var earnedDeducted = earned - 1000
                    } else {
                        var earnedDeducted = '0';
                    }

                //this.deductedEarning = parseFloat(this.deductedEarning);
                this.incomeAfterTax = +federalTaxScale(earnedDeducted).toFixed(2);
                //this.earning = parseFloat(this.earning);
                //this.stateTaxPercent = parseFloat(this.stateTaxPercent);
                this.incomeAfterTax += +stateTaxScale(earnedDeducted, this.stateTaxPercent).toFixed(2);
                this.selfEmployed = parseFloat(this.selfEmployed);
                this.incomeAfterTax += +medicareTaxScale(earnedDeducted, this.selfEmployed).toFixed(2);
                this.incomeAfterTax += +socialSecurityTaxScale(earnedDeducted, this.selfEmployed).toFixed(2);
                this.incomeAfterTax = this.steps[i] - this.incomeAfterTax;
                this.incomeAfterTax = parseFloat(this.incomeAfterTax.toFixed(2));

                this.benefits = +ssiScale(this.steps[i], this.ssiAmount);
                this.benefits += +ssdiScale(this.steps[i], this.ssdiAmount);
                this.benefits += +snapScale(this.steps[i], this.snapAmount, this.disabled, this.snapCutOff);
                this.benefits += +sec8Scale(this.steps[i], this.sec8Amount);
                this.benefits += +energyScale(this.steps[i], this.energyAmount);

                var benefitsTotal = this.ssiAmount + this.ssdiAmount + this.snapAmount + this.sec8Amount + this.energyAmount;
                this.benefits = this.benefits - benefitsTotal;
                this.benefits = Math.abs(this.benefits);
                this.benefits = parseFloat(this.benefits.toFixed(2));


                this.incomePlusBenefits[i] = this.incomeAfterTax + this.benefits;
                    /*var result =  + +earned; // from old graph; in garbage file. Used steppedResults instead of steppedResultsLoss
                    var result = result.toFixed(2);
                    this.steppedResults.push(result);*/
                }
            },

            stepper: function () {
//              this.steppedColors = [];
                this.steppedResultsLoss = [];
                for (i = 0; i < this.steps.length; i++){
                    var earned = this.steps[i];
                        if (earned >= 1000) {
                            var earnedDeducted = earned -1000
                        } else {
                            var earnedDeducted = '0';
                        }
                    
                    var expense = +ssiScale(earned, this.ssiAmount);
                    expense += +ssdiScale(earned, this.ssdiAmount);
                    expense += +snapScale(earned, this.snapAmount, this.disabled, this.snapCutOff);
                    expense += +sec8Scale(earned, this.sec8Amount);
                    expense += +energyScale(earned, this.energyAmount);
                    expense += +federalTaxScale(earnedDeducted);
                    expense += +stateTaxScale(earned, this.stateTaxPercent);
                    expense += +medicareTaxScale(earnedDeducted, this.selfEmployed);
                    expense += +socialSecurityTaxScale(earnedDeducted, this.selfEmployed);

                    var result = -expense + +earned; // from old graph; in garbage file. Used steppedResults instead of steppedResultsLoss
                    var result = result.toFixed(2);
                    this.steppedResults.push(result);
/*
                    if (result >= 0){
                        this.steppedColors.push('green');
                    } else {
                        this.steppedColors.push('red');
                    }
*/
                    var result2 = expense /*+ +earned*/; // from old graph; in garbage file. Used steppedResults instead of steppedResultsLoss
                    var result2 = result2.toFixed(2);
                    this.steppedResultsLoss.push(result2);
                }
            }
        }
    })

//TODO https://github.com/apertureless/vue-chartjs
//TODO https://vuejs.org/v2/examples/svg.html
</script>

</body>
</html>