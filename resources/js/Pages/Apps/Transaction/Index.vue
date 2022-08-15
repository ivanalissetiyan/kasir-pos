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
                                    <input type="text" class="form-control" placeholder="scan or input barcode">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Product Name</label>
                                    <input type="text" class="form-control" placeholder="product name" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Qty</label>
                                    <input type="number" class="form-control text-center" placeholder="Qty" min="1">
                                </div>
                                <div class="text-end">
                                    <button
                                        class="btn btn-warning btn-md border-0 shadow text-uppercase mt-3 me-2">Clear</button>
                                    <button class="btn btn-primary btn-md border-0 shadow text-uppercase mt-3 me-2">Add
                                        Item</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card border-0 rounded-3 shadow border-top-success">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 col-4">
                                        <h4 class="fw-bold">Grand Total</h4>
                                    </div>
                                    <div class="col-md-8 col-8 text-end">
                                        <h4 class="fw-bold">Rp. 0</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card border-0 rounded-3 shadow">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <Label class="fw-bold">Cashier</Label>
                                        <!-- Value auth.user.name itu sama aja halnya seperti auth()->usser() / Auth::user() -->
                                        <input class="form-control" type="text" :value="auth.user.name" readonly>
                                    </div>
                                    <div class="col-md-6 float-end">
                                        <label class="fw-bold">Customer</label>
                                        <VueMultiselect v-model="customer_id" label="name" track-by="name" :options="customers"></VueMultiselect>
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
        customers: Array
    }
}
</script>

<style>
</style>