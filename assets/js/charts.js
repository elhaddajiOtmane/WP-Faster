/**
 * Generate Chart JS options based on constants and passed annotation variables
 *
 * @checkthis https://dev.to/t/chartjs
 *
 * @param  {object} annotations Annotation object containing an array of annotations
 * @return {object}             Options object
 */
function chartJSOptions(annotationChartHolder, max = null) {
    const fontFamily = '"Proxima Vara", -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif, "Segoe UI Emoji", "Apple Color Emoji"';

    Chart.defaults.font.family = fontFamily;

    const lineOptionsArray = {
        plugins: {
            tooltip: {
                intersect: false,
                mode: 'index'
            },
            legend: {
                position: 'top',
                align: 'start',
                labels: {
                    usePointStyle: true,
                    //boxWidth: 6,
                    fontColor: '#000000'
                },
            },
            scales: {
                yAxes: [{
                    ticks: {
                        fontFamily: fontFamily,
                        fontColor: '#000000',
                        beginAtZero: true,
                        min: 0,
                    },
                    pointLabels: {
                        fontFamily: fontFamily,
                        fontColor: '#000000'
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontFamily: fontFamily,
                        fontColor: '#000000',
                        autoSkip: true,
                        maxTicksLimit: 4,
                        minRotation: 0,
                        maxRotation: 0,
                        //display: false,
                    }
                }]
            }
        }
    };

    // Use max = 100 for percentage values
    if (max === 100) {
        lineOptionsArray.scales.yAxes[0].ticks.max = 100;
    }

    return lineOptionsArray;
}

document.addEventListener('DOMContentLoaded', function () {
    /**
     * Options array for the line chart
     *
     * @type array
     */
    const fontFamily = '"Proxima Vara", -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif, "Segoe UI Emoji", "Apple Color Emoji"';



    if (document.getElementById('ttfb-bar-values')) {
        /**
         * Get chart holder element
         */
        const ttfbBarValuesChartHolder = document.getElementById('ttfb-bar-values');

        /**
         * Get chart holder data attributes (labels and datasets)
         */
        let dateArrayPiped = ttfbBarValuesChartHolder.dataset.labels.split('|'),
            datasets = ttfbBarValuesChartHolder.dataset.values.split('|'),
            datasetsBeacon = ttfbBarValuesChartHolder.dataset.valuesBeacon.split('|'),
            ctxTtfbBarValues = ttfbBarValuesChartHolder.getContext('2d');

        /**
         * Build the speed factors chart
         */
        let chartTtfbBarValues = new Chart(ctxTtfbBarValues, {
            type: 'line',

            data: {
                labels: dateArrayPiped,
                datasets: [{
                    label: 'Time to First Byte',
                    fill: false,
                    backgroundColor: '#0652DD',
                    borderColor: '#0652DD',
                    borderWidth: 2,
                    pointRadius: 0,
                    data: datasets,
                    tension: 0.2
                },
                {
                    label: 'Time to First Byte (Beacon)',
                    fill: false,
                    backgroundColor: '#009432',
                    borderColor: '#009432',
                    borderWidth: 2,
                    pointRadius: 0,
                    data: datasetsBeacon,
                    tension: 0.2
                }]
            },

            options: chartJSOptions(ttfbBarValuesChartHolder)
        });
    }



    if (document.getElementById('chartjs-payload')) {
        /**
         * Get chart holder element
         */
        const payloadChartHolder = document.getElementById('chartjs-payload');

        /**
         * Get chart holder data attributes (labels and datasets)
         */
        let dateArrayPiped = payloadChartHolder.dataset.labels.split('|')
            siteAssetsImg = payloadChartHolder.dataset.assetsImg.split(','),
            siteAssetsCss = payloadChartHolder.dataset.assetsCss.split(','),
            siteAssetsJs = payloadChartHolder.dataset.assetsJs.split(','),
            ctxPayloadValues = payloadChartHolder.getContext('2d');

        /**
         * Build the security chart
         */
        let chartPayloadValues = new Chart(ctxPayloadValues, {
            type: 'line',

            data: {
                labels: dateArrayPiped,
                datasets: [{
                    label: 'Images',
                    data: siteAssetsImg,
                    fill: false,
                    borderColor: '#FD95B4', // Mint Hint
                    borderWidth: 2,
                    pointRadius: 0,
                    backgroundColor: '#FD95B4',
                    tension: 0.2
                },
                {
                    label: 'JavaScript',
                    data: siteAssetsJs,
                    fill: false,
                    borderColor: '#A6DAF0', // Mint Hint
                    borderWidth: 2,
                    pointRadius: 0,
                    backgroundColor: '#A6DAF0',
                    tension: 0.2
                },
                {
                    label: 'Stylesheets',
                    data: siteAssetsCss,
                    fill: false,
                    borderColor: '#E1BDDF', // Mint Hint
                    borderWidth: 2,
                    pointRadius: 0,
                    backgroundColor: '#E1BDDF',
                    tension: 0.2
                }]
            },

            options: chartJSOptions(payloadChartHolder)
        });



        /**
         * Get chart holder element
         */
        const payloadRequestsChartHolder = document.getElementById('chartjs-payload-requests');

        /**
         * Get chart holder data attributes (labels and datasets)
         */
        let dateRequestsArrayPiped = payloadRequestsChartHolder.dataset.labels.split('|')
            siteRequests = payloadRequestsChartHolder.dataset.requests.split(','),
            ctxPayloadRequestsValues = payloadRequestsChartHolder.getContext('2d');

        /**
         * Build the security chart
         */
        let chartPayloadRequestsValues = new Chart(ctxPayloadRequestsValues, {
            type: 'line',

            data: {
                labels: dateRequestsArrayPiped,
                datasets: [{
                    label: 'Requests',
                    data: siteRequests,
                    fill: false,
                    borderColor: '#72C8B7', // Mint Hint
                    borderWidth: 2,
                    pointRadius: 0,
                    backgroundColor: '#72C8B7',
                    tension: 0.2
                }]
            },

            options: chartJSOptions(payloadRequestsChartHolder)
        });
    }
});
