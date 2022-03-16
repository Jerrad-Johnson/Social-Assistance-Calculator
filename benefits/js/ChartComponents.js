
// Charting
Vue.component('doughnut-chart', {
    extends: VueChartJs.Doughnut,
    mixins: [VueChartJs.mixins.reactiveProp],
    props: ['chartData', 'options', 'difference'],
    mounted () {
        this.renderChart(this.chartData, {
            responsive: true,
            maintainAspectRatio: false,
        });
    }
});

Vue.component('bar-chart', {
    extends: VueChartJs.Bar,
    mixins: [VueChartJs.mixins.reactiveProp],
    props: ['chartData', 'options'],
    mounted () {
        this.renderChart(this.chartData, {responsive: true, maintainAspectRatio: false});
    }
});
