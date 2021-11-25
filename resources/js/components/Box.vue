<template>
    <div>
        <h5>Trollbox</h5>
        <p>Coming soon</p>
    </div>
</template>

<script>
export default {
    props: [
        "who"
    ],
    data: function() {
        return {
            messages: [],
        };
    },

    mounted() {
        //set interval get messages every few secs
    },

    methods: {
        //axios get messages
        getMessages: function() {

        },
        createMessage: function() {

        }
    },

    watch: {

    }
}

</script>
