<template>

    <Head>
        <title>Dashboard - Aplikasi Kasir</title>
    </Head>

    <main class="c-main">
        <div class="container-fluid">
            <div class="fade-in">

                <div class="row">
                    <div class="col-md-8">
                        <div v-if="hasAnyPermission(['dashboard.sales_chart'])"
                            class="card border-0 rounded-3 shadow border-top-purple">
                            <div class="card-header">
                                <span class="font-weight-bold"><i class="fa fa-chart-bar"></i> Penjualan 7 Hari</span>
                            </div>
                            <div class="card-body">
                                    <BarChart :chartData="chartSellWeek" :options="options" />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div v-if="hasAnyPermission(['dashboard.sales_today'])"
                            class="card border-0 rounded-3 shadow border-top-info mb-4">
                            <div class="card-header">
                                <span class="font-weight-bold"><i class="fa fa-chart-line"></i> Penjualan Hari ini</span>
                            </div>
                            <div class="card-body">
                                <strong>{{ count_sales_today }}</strong>Penjualan
                                <hr>
                                <h5 class="fw-bold">Rp {{ formatPrice(sum_sales_today)}}</h5>
                            </div>
                        </div>

                    <div v-if="hasAnyPermission(['dashboard.profits_today'])"
                        class="card border-0 rounded-3 shadow border-top-success">
                        <div class="card-header">
                            <span class="font-weight-bold"><i class="fa fa-chart-bar"></i> Profit Hari ini</span>
                        </div>
                        <div class="card-body">
                            <h5 class="fw-bold">Rp. {{ formatPrice(sum_profits_today) }}</h5>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-6">
                    <div v-if="hasAnyPermission(['dashboard.best_selling_product'])"
                        class="card border-0 rounded-3 shadow border-top-warning">
                        <div class="card-header">
                            <span class="font-weight-bold"><i class="fa fa-chart-pie"></i> Product Terlaris</span>
                        </div>
                        <div class="card-body">

                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div v-if="hasAnyPermission(['dashboard.product_stock'])"
                        class="card border-0 rounded-3 shadow border-top-danger">
                        <div class="card-header">
                            <span class="font-weight-bold"><i class="fa fa-box-open"></i> Product Stock</span>
                        </div>
                        <div class="card-body">

                        </div>
                    </div>
                </div>
            </div>

        </div>
        </div>
    </main>

</template>

<script>

// Import layout
import LayoutApp from '../../../Layouts/App.vue';

// Import inertiajs
import { Head } from '@inertiajs/inertia-vue3';

//import ref from vue
import { ref } from 'vue';


// Chart
import { BarChart } from 'vue-chart-3';
import { Chart, registerables } from "chart.js";

// Register chart
Chart.register(...registerables);

export default {
    // layout
    layout: LayoutApp,

    // Register components
    components: {
        Head,
        BarChart, 
    },

    props:{
        // Total penjualan hari ini
        count_sales_today: Number,

        // Jumlah (Rp) penjualan hari ini
        sum_sales_today: Number,

        // Jumlah profit/labar hari ini
        sum_profits_today: Number,

        // Chart sales
        sales_date: Array,
        grand_total: Array,
    },

    setup(props) {
        
        //method random color
        function randomBackgroundColor(length) {
            var data = [];
            for (var i = 0; i < length; i++) {
                data.push(getRandomColor());
            }
            return data;
        }

        //method generate random color
        function getRandomColor() {
            var letters = '0123456789ABCDEF'.split('');
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }



        // Option Chart
        const options = ref({
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                },
                title: {
                    display: false,
                },                
            },
            beginZero: true
        });

        // Chart sell week
        const chartSellWeek = {
            labels: props.sales_date,
            datasets: [{
                data: props.grand_total,
                backgroundColor: randomBackgroundColor(props.sales_date.length),
            }, ],

        };

        return {
            options,
            chartSellWeek,
        };

    }

}

</script>

<style>

</style>