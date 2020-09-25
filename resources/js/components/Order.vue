<template>
    <div class="container">
        <div class="card">
            <div class="card-header">Holdings</div>
            <div class="card-body">
                <p>AMPL: {{ ampl }}</p>
                <p>USDT: {{ usdt }}</p>
            </div>
        </div>
        <br>
        <div class="card">
            <div class="card-header">Current Price</div>
            <div class="card-body">
                <p>AMPL: ${{ currentPrice }}</p>
            </div>
        </div>
        <br>
        <div class="card">
            <div class="card-header">Orders to be actioned</div>
            <div class="card-body">
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Pair</th>
                            <th>Side</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>When</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody v-if="orders.length > 0">
                        <tr v-for="order in orders" :key="order.id">
                            <td>{{ order.id }}</td>
                            <td>{{ order.pair }}</td>
                            <td>{{ order.side }}</td>
                            <td>{{ order.type }}</td>
                            <td>{{ order.amount }}</td>
                            <td>{{ order.when }}</td>
                            <td><span v-on:click="del(order.id)" class="delete-button">x</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- orders on db -->
        <br>
        <div class="card">
            <div class="card-header">Order (NEED TO MAKE A 'IMMEDIATELY BUTTON')</div>
            <div class="card-body">
                <form method="POST" @submit.prevent="processForm" id="order-form" enctype="multipart/form-data">
                    <div class="inputs-container">
                        <div class="field">
                            <input v-model="fields.type" value="limit" type="radio" id="radio-limit" name="switch-type" />
		                    <label for="radio-limit">Limit</label>
		                    <input v-model="fields.type" value="market" type="radio" id="radio-market" name="switch-type" checked/>
		                    <label for="radio-market">Market</label>
                        </div>
                        <div class="field">
                            <input v-model="fields.side" value="buy" type="radio" id="radio-buy" name="switch-side" />
		                    <label for="radio-buy">Buy</label>
		                    <input v-model="fields.side" value="sell" type="radio" id="radio-sell" name="switch-side" checked/>
		                    <label for="radio-sell">Sell</label>
                        </div>
                        <div class="field">
                            <label class="label">Market Pair</label>
                            <input v-model="fields.pair">
                        </div>
                        <div class="field">
                            <label class="label">Amount %</label>
                            <input v-model="fields.amount" type="number" min="10" max="100">
                        </div>

                        <!-- gettimelabel(num) -->
                        <!-- gettimevalue(num) -->

                        <div class="field">
                            <label for="time">Trigger Time</label>
                            <select v-model="fields.when" id="time" name="time" form="order-form">
                                <option v-for="num in 24" v-bind:key="num" :value="getTimeValue(num)">{{ getTimeLabel(num) }}</option>
                            </select>
                        </div>

                        <!-- <div v-for="num in 10" v-bind:key="num">
                            <span>
                            {{num}}
                            </span>
                            <span>|</span>
                        </div> -->
                    </div>
                    <br>

                    <div class="submit-button">
                        <button type="submit">Place Order</button>
                        <!-- <button @click="processForm(e)" type="submit">Place Order</button> -->
                    </div>

                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
          data: function() {
            return {
                ampl: "0",
                usdt: "0",
                currentPrice: 0,
                orders: [],
                fields: {
                    type: "market",
                    side: "sell",
                    pair: "AMPL-USDT",
                    amount: 100,
                    when: 2,
                }
            };
        },
        mounted() {
            this.getBal('ampl');
            this.getBal('usdt');
            this.triggers();
            this.getPrice('AMPL-USDT');
        },

        methods: {

            del: function(id) {
                axios.delete("trigger/" + id).then(response => {

                    this.triggers();

                }).catch(error => {
                    console.log('Error');
                }); 
            },

            getPrice: function(coin) {
                var that = this;
                var request = new XMLHttpRequest();
                var path = "price/" + coin;
                request.open("GET", path, true);
                request.onload = function() {
                    var price = request.response;
                    that.currentPrice = price;
                }
                request.send();
            },

            getTimeLabel: function(time) {

                time -= 1;

                return time >= 10 ? time + "00" : "0" + time + "00";  
            },

            getTimeValue: function(time) {

                time -= 1;

                return time;
            },

            processForm: function() {

                axios.post('addtrigger', this.fields).then(response => {

                this.triggers();


                }).catch(error => {
                    console.log('Error');
                }); 

            },

            triggers: function() {
                axios.get('triggers').then(response => {
                    this.orders = response.data;
                });
            },

            getBal: function(coin) {

                var that = this;
                var request = new XMLHttpRequest();
                var path = "balance/" + coin;
                request.open("GET", path, true);
                request.onload = function() {
                    var bal = request.response;

                    if (coin === "ampl") {
                        that.ampl = bal;
                        that.amount = bal;
                    } 
                    
                    if (coin === "usdt") {
                        that.usdt = bal;
                    }
                }
                request.send();
            }
        }
    }

</script>
<style>

.field {
    float: right;
    clear: right;
}

.inputs-container {
    width: 300px;
    margin-left: auto;
    margin-right: auto;
}

.submit-button button {
    width: 100%;
    margin-top: 2%;
}

.delete-button {
    cursor: pointer;
}

</style>


