<template>
    <div class="mr-5 ml-5 mt-5 mb-5 row">
        <div class="col-4">
            <input type="text" v-model="bof" class="form-control mb-1 mt-2">
            <button @click="fetchBalanceData" class="btn btn-success">Get Record</button>
            <table v-if="bc.length > 0" class="table table-borderless table-striped table-hover table-vcenter">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Balance</th>
                    <th>Balance ($)</th>
                </tr>
                </thead>
                <tbody>
                    <tr v-for="b in bc">
                        <td>{{ formatDate(b.created_at) }}</td>
                        <td>{{ b.balance }}</td>
                        <td>{{ b.balance_usd }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        "bdr",
    ],
    data: function() {
        return {
            bc: [],
            bof: "",
        };
    },

    methods: {
        formatDate: function(date) {
           var obj = new Date(date);
           return obj.toLocaleDateString();
        },

        addData: function addData() {
            this.dataset.push(this.dataentry);
            this.labels.push(this.datalabel);
            this.datalabel = '';
            this.dataentry = '';
        },

        fetchBalanceData: function() {
            axios.get(this.bdr, {
                params: {
                    c: this.bof,
                }
            }).then(response => {
                this.bc = response.data;
            });
        }
    },
}

</script>
<style>

</style>
