<template>
    <div class="mr-5 ml-5 mt-5 mb-5 row">
        <div class="col-4">
            <table class="table table-bordered table-striped table-hover table-vcenter">
                <thead>
                <tr>
                    <th></th>
                    <th colspan="5">Real</th>
                    <th></th>
                    <th colspan="5">If Holding</th>
                    <th>If $</th>
                </tr>
                <tr>
                    <th></th>
                    <th colspan="2">Balance {{ s1 }}</th>
                    <th colspan="2">Balance {{ s2 }}</th>
                    <th>Total</th>
                    <th></th>
                    <th colspan="2">Balance {{ s1 }}</th>
                    <th colspan="2">Balance {{ s2 }}</th>
                    <th>Total</th>
                    <th>Total</th>
                </tr>
                <tr>
                    <th>Date</th>
                    <th>{{ s1 }}</th>
                    <th>$</th>
                    <th>{{ s2 }}</th>
                    <th>$</th>
                    <th>$</th>
                    <th>Δ</th>
                    <th>{{ s1 }}</th>
                    <th>$</th>
                    <th>{{ s2 }}</th>
                    <th>$</th>
                    <th>$</th>
                    <th>$</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in data">
                    <td>{{ formatDate(item.created_at) }}</td>
                    <td>{{ item.balance_s1.toFixed(2) }}</td>
                    <td>{{ item.balance_s1_usd.toFixed(2) }}</td>
                    <td>{{ item.balance_s2.toFixed(2) }}</td>
                    <td>{{ item.balance_s2_usd.toFixed(2) }}</td>
                    <td class="bg-info text-light">{{ (item.balance_s1_usd + item.balance_s2_usd).toFixed(2) }}</td>
                    <td class="text-light" :class="((item.balance_s1_usd + item.balance_s2_usd) - (item.wbw_usd_1 + item.wbw_usd_2)) > 0 ? 'bg-success' : 'bg-danger'"
                    >{{ ((item.balance_s1_usd + item.balance_s2_usd) - (item.wbw_usd_1 + item.wbw_usd_2)).toFixed(2) }}</td>
                    <td>{{ item.input_symbol1.toFixed(2) }}</td>
                    <td>{{ item.wbw_usd_1.toFixed(2) }}</td>
                    <td>{{ item.input_symbol2.toFixed(2) }}</td>
                    <td>{{ item.wbw_usd_2.toFixed(2) }}</td>
                    <td class="bg-dark text-light">{{ (item.wbw_usd_1 + item.wbw_usd_2).toFixed(2) }}</td>
                    <td class="bg-secondary text-light">{{ (item.input_symbol1_usd + item.input_symbol2_usd).toFixed(2) }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>{{ formatDate(new Date()) }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="bg-info text-light"></td>
                    <td class="text-light"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="bg-dark text-light"></td>
                    <td class="bg-secondary text-light"></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        "value",
        "push-lasts"
    ],
    data: function() {
        return {
            data: [],
            pricec1: null,
            pricec2: null,
            s1: null,
            s2: null,
        };
    },

    mounted() {
    },

    methods: {
        formatDate: function(date) {
            var obj = new Date(date);
            return obj.toLocaleDateString();
        },
        getData: function(s1, s2) {
            this.s1 = s1;
            this.s2 = s2;
            axios.get('/get_pair_data', {
                params: {
                    s1,
                    s2,
                }
            }).then(response => {
                this.data = response.data;
            });
        }
    },
    watch: {
        value: function(val) {
            if (val.length == 2) {
                this.getData(val[0].name, val[1].name);
            }
        },
        pushLasts: function(val) {
            this.pricec1 = val[0].s1;
            this.pricec2 = val[1].s2;
        }

    }
}

</script>
