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
            <div class="card-header">Orders on exchange</div>
            <div class="card-body">
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>Pair</th>
                            <th>Side</th>
                            <th>Type</th>
                            <th>Price ($)</th>
                            <th>Amount</th>
                            <th>Open</th>
                            <th>Created at</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody v-if="exchange_orders.length > 0">
                        <tr v-for="exchange_order in exchange_orders" :key="exchange_order.id">
                            <td>{{ exchange_order.symbol }}</td>
                            <td>{{ exchange_order.side }}</td>
                            <td>{{ exchange_order.type }}</td>
                            <td>{{ exchange_order.price }}</td>
                            <td>{{ exchange_order.size }}</td>
                            <td>{{ exchange_order.isActive }}</td>
                            <td>{{ new Date(exchange_order.createdAt) }}</td>
                            <td><span v-on:click="cancel(exchange_order.id)" class="delete-button">x</span></td>
                        </tr>
                    </tbody>
                </table>
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
            <div class="card-header">Order</div>
            <div class="card-body">
                <form method="POST" @submit.prevent="processForm" id="order-form" enctype="multipart/form-data">
                    <div class="inputs-container">
                        <div class="field">
                            <input @change="priceStatus" v-model="fields.type" value="limit" type="radio" id="radio-limit" name="switch-type" />
		                    <label for="radio-limit">Limit</label>
		                    <input @change="priceStatus" v-model="fields.type" value="market" type="radio" id="radio-market" name="switch-type" checked/>
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
                        <div v-if="fields.type === 'limit'" class="field">
                            <label class="label">Price $</label>
                            <input v-model="fields.price" type="number" min="0.5" max="2" step="0.01">
                        </div>

                        <!-- gettimelabel(num) -->
                        <!-- gettimevalue(num) -->

                        <div class="field">
                            <label for="time">Trigger Time</label>
                            <select :disabled="fields.now" v-model="fields.when" id="time" name="time" form="order-form">
                                <option v-for="num in 24" v-bind:key="num" :value="getTimeValue(num)">{{ getTimeLabel(num) }}</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="now">Immediately</label>
                            <input v-model="fields.now" type="checkbox">
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
                exchange_orders: [],
                fields: {
                    type: "market",
                    side: "sell",
                    pair: "AMPL-USDT",
                    amount: 100,
                    when: 2,
                    price: null,
                    now: false
                }
            };
        },
        mounted() {
            this.getBal('ampl');
            this.getBal('usdt');
            this.exchangeOrders();
            this.triggers();
            this.getPrice('AMPL-USDT');
        },

        methods: {

            priceStatus: function() {
                this.fields.price = this.fields.type === "market" ? null : this.fields.price;
            },

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
                this.exchangeOrders();

                }).catch(error => {
                    console.log('Error');
                }); 

            },

            triggers: function() {
                axios.get('triggers').then(response => {
                    this.orders = response.data;
                });
            },

            exchangeOrders: function() {
                axios.get('orders').then(response => {
                    this.exchange_orders = response.data;
                });
            },

            cancel: function(orderId) {

                // axios.get('cancel/'.$orderId).then(response => {
                //     console.log(response);
                // }); 

                axios.get('cancel/' + orderId).then(response => {
                    if (!response.status == 200) {
                        console.log('ERROR DELETING');
                    } 
                });

                this.exchangeOrders();
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


