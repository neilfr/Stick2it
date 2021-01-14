<template>
    <div class="container">
        <h1>Log Entries</h1>
        <ul>
            <li v-for="logentry in logentries" :key="logentry.id">
                {{logentry.consumed_at}}
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props:{
        logentries: Array,
    }
}
</script>
