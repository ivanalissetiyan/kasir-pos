<template>
    <Head>
        <title>Customers - Aplikasi Kasir</title>
    </Head>
    <main class="c-main">
        <div class="container-fluid">
            <div class="fade-in">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 rounded-3 shadow border-top-blue">
                            <div class="card-header">
                                <span class="font-weight-bold"><i class="fa fa-user-circle"></i> Customer</span>
                            </div>
                            <div class="card-body">
                                <form @submit.prevent="handleSearch">
                                    <div class="input-group mb-3">
                                        <Link href="/apps/customers/create"
                                            v-if="hasAnyPermission(['customers.create'])"
                                            class="btn btn-primary input-group-text"> <i
                                            class="fa fa-plus-circle me-2"></i> New </Link>
                                        <input type="text" v-model="search" class="form-control"
                                            placeholder="search by Customer Name....">
                                        <button class="btn btn-primary input-group-text" type="submit"><i
                                                class="fa fa-search me-2"></i>Search</button>
                                    </div>
                                </form>
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">No Telfon</th>
                                            <th scope="col">Alamat</th>
                                            <th scope="col" class="text-center" style="width:20%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(customer, index) in customers.data" :key="index">
                                            <td>{{ customer.name }}</td>
                                            <td>{{ customer.no_telp }}</td>
                                            <td>{{ customer.address }}</td>
                                            <td class="text-center">
                                                <Link :href="`/apps/customers/${customer.id}/edit`"
                                                    v-if="hasAnyPermission(['customers.edit'])"
                                                    class="btn btn-success btn-sm me-2"><i
                                                    class="fa fa-pencil-alt me-1"></i>Edit</Link>
                                                <button @click.prevent="destroy(customer.id)"
                                                    v-if="hasAnyPermission(['customers.delete'])"
                                                    class="btn btn-danger btn-sm"><i
                                                        class="fa fa-trash"></i>Delete</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <Pagination :links="customers.links" align="end" />
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

    // Import component pagination
    import Pagination from '../../../Components/Pagination.vue';

    // Import head and link from inertia
    import { Head, Link } from '@inertiajs/inertia-vue3';
    
    // import ref from vue
    import { ref } from 'vue';

    // import inertia adapter
    import { Inertia } from '@inertiajs/inertia';

    // Import Sweet Alert 2
    import Swal from 'sweetalert2';

    export default {

    // Layout
    layout: LayoutApp,

    // Register Components
    components: {
        Head,
        Link,
        Pagination
    },
    
    props: {
        customers: Object,
    },

    setup() {
        //define state search
        const search = ref('' || (new URL(document.location)).searchParams.get('q'));

        //define method search
        const handleSearch = () => {
            Inertia.get('/apps/customers', {

                //send params "q" with value from state "search"
                q: search.value,
            });
        }

        // Define method destroy
        const destroy = (id) => {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
                .then((result) => {
                    if (result.isConfirmed) {

                        Inertia.delete(`/apps/customers/${id}`);

                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Customer Deleted Successfully.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                })
        }

        //return
        return {
            search,
            handleSearch,
            destroy,
        }
    }


}

</script>

<style>

</style>