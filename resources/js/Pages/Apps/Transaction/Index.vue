<template>
    <Head>
        <title>Transaction - Aplikasi Kasir</title>
    </Head>
    <main class="c-main">
        <div class="container-fluid">
            <div class="fade-in">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card-border-0 rounded-3 shadow">
                            <div class="card-body">
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                                    <input type="text" class="form-control" v-model="barcode"
                                        placeholder="scan or input barcode" @keyup="searchProduct">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nama Produk</label>
                                    <input type="text" class="form-control" :value="product.title"
                                        placeholder="product name" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Qty</label>
                                    <input type="number" class="form-control text-center" v-model="qty" min="1">
                                </div>
                                <div class="text-end">
                                    <button @click.prevent="clearSearch"
                                        class="btn btn-warning btn-md border-0 shadow text-uppercase mt-3 me-2"
                                        :disabled="!product.id">Clear</button>
                                    <button @click.prevent="addToCart"
                                        class="btn btn-primary btn-md border-0 shadow text-uppercase mt-3"
                                        :disabled="!product.id">Add Item</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">

                        <div v-if="session.error" class="alert alert-danger">
                            {{ session.error }}
                        </div>

                        <div v-if="session.success" class="alert alert-success">
                            {{ session.success }}
                        </div>

                        <div class="card border-0 rounded-3 shadow border-top-success">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 col-4">
                                        <h4 class="fw-bold">Grand Total</h4>
                                    </div>
                                    <div class="col-md-8 col-8 text-end">
                                        <h4 class="fw-bold">Rp. {{ formatPrice(grandTotal) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card border-0 rounded-3 shadow">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="fw-bold">Cashier</label>
                                        <!-- Value auth.user.name itu sama aja halnya seperti auth()->usser() / Auth::user() -->
                                        <input class="form-control" type="text" :value="auth.user.name" readonly>
                                    </div>
                                    <div class="col-md-6 float-end">
                                        <label class="fw-bold">Customer</label>
                                        <VueMultiselect v-model="customer_id" label="name" track-by="name"
                                            :options="customers"></VueMultiselect>
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr style="background-color: #e6e6e7;">
                                            <th scope="col">#</th>
                                            <th scope="col">Nama Produk</th>
                                            <th scope="col">Harga</th>
                                            <th scope="col">Qty</th>
                                            <th scope="col">Total Harga</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="cart in carts" :key="cart.id">
                                            <td class="text-center">
                                                <button @click.prevent="destroyCart(cart.id)" class="btn btn-danger btn-sm rounded-pill"><i class="fa fa-trash"></i></button>
                                            </td>
                                            <td>{{ cart.product.title }}</td>
                                            <td>Rp. {{ formatPrice(cart.product.sell_price) }}</td>
                                            <td class="text-center"> {{ cart.qty }}</td>
                                            <td class="text-end">Rp. {{ formatPrice(cart.price) }}</td>
                                        </tr>
                                        <tr>
                                           <td colspan="4" class="text-end fw-bold" style="background-color: #e6e6e7;">Total</td> 
                                           <td colspan="4" class="text-end fw-bold" style="background-color: #e6e6e7;">Rp, {{ formatPrice(carts_total) }}</td> 
                                        </tr>
                                    </tbody>
                                </table>
                                <hr>
                                <div class="d-flex align-items-end flex-column bd-highlight mb-3">
                                    <div class="mt-auto bd-highlight">
                                        <label>Discount (Rp.)</label>
                                        <input type="number" class="form-control" placeholder="Pay (Rp.)">
                                    </div>
                                    <div class="bd-highlight mt-4">
                                        <label>Pembayaran (Rp.)</label>
                                        <input type="number" class="form-control" placeholder="Pay (Rp.)">
                                    </div>
                                </div>
                                <div class="text-end mt-4">
                                    <button
                                        class="btn btn-warning btn-md border-0 shadow text-uppercase me-2">Cancel</button>
                                    <button class="btn btn-primary btn-md border-0 shadow text-uppercase">Bayar Pesanan
                                        & Print</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
//import layout
import LayoutApp from '../../../Layouts/App.vue';

//import Heade from Inertia
import { Head } from '@inertiajs/inertia-vue3';

// Import VueMultiselect
import VueMultiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';

// import ref from vue
import { ref } from 'vue';

// import axios
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';

export default {
    //layout
    layout: LayoutApp,

    //register components
    components: {
        Head,
        VueMultiselect
    },

    // Props
    props: {
        auth: Object,
        customers: Array,
        carts_total: Number,
        session: Object,
        carts: Array
    },

    // Compotition API
    setup(props) {

        // Define state
        const barcode = ref('');
        const product = ref({});
        const qty = ref(1);

        // Method search product
        const searchProduct = () => {

            // Fetch with axios
            axios.post('/apps/transactions/searchProduct', {

                // Send Data Barcode
                barcode: barcode.value

            }).then(response => {
                if (response.data.success) {
                    // Assign response to state "product"
                    product.value = response.data.data;
                } else {
                    // Set data "product" to empty object
                    product.value = {};
                }
            });
        }

        // Method "Clear Search"
        const clearSearch = () => {

            // Set state "product" to empty object
            product.value = {};

            // Set state "barcode" to empty string
            barcode.value = '';

            // Set State "qty" to empty number qty
            qty.value = (1);
        }

        // Define State Grand Total
        const grandTotal = ref(props.carts_total);

        // Method addToCart
        const addToCart = () => {
            // Send data to server
            Inertia.post('/apps/transactions/addToCart', {

                // Data
                product_id: product.value.id,
                qty: qty.value,
                sell_price: product.value.sell_price,
            }, {
                onSuccess: () => {
                    // Call Method "clearSearch"
                    clearSearch();

                    // Set qty to "1"
                    qty.value = 1;

                    // Update state "grand total"
                    grandTotal.value = props.carts_total;
                },
            });
        }

                    //method "destroyCart"
            const destroyCart = (cart_id) => {
                Inertia.post('/apps/transactions/destroyCart', {
                    cart_id: cart_id
                }, {
                    onSuccess: () => {

                        //update state "grandTotal"
                        grandTotal.value = props.carts_total;
                    },
                })
            }


        return {
            barcode,
            product,
            searchProduct,
            clearSearch,
            qty,
            grandTotal,
            addToCart,
            destroyCart
        }
    }
}
</script>

<style>
</style>