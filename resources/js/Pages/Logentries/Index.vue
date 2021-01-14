<template>
    <div class="container">
        <h1>Log Entries</h1>
        <ul>
            <li v-for="logentry in logentries.data" :key="logentry.id">
                {{logentry.consumed_at}}
                {{logentry.food_description}}
                {{logentry.food_alias}}
                {{logentry.quantity}}
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props:{
        logentries: Object,
    }
}
</script>
