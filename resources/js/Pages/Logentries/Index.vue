<template>
    <div class="container">
        <div class='flex justify-between'>
            <h1>Log Entries</h1>
            <button @click="add">Add Log Entry</button>
        </div>
        <table>
            <tr>
                <th>Date / Time</th>
                <th>Alias</th>
                <th>Description</th>
                <th>Quantity</th>
            </tr>
            <tr v-for="logentry in logentries.data" :key="logentry.id">
                <td>{{logentry.consumed_at}}</td>
                <td>{{logentry.food.description}}</td>
                <td>{{logentry.food.alias}}</td>
                <td>{{logentry.quantity}}</td>
            </tr>
        </table>
    </div>
</template>

<script>
export default {
    props:{
        logentries: Object,
    },
    methods:{
        add () {
            let url = `${this.$route("logentries.create")}`;
            this.$inertia.visit(url);
        }
    }
}
</script>
