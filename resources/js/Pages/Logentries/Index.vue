<template>
    <div class="container">
        <h1>Log Entries</h1>
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
    }
}
</script>
