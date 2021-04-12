<template>
    <div class="container">
        <div>
            <label for="from">From:</label>
            <input type="date" name="from" id="from" v-model='from' @change="goToPageOne">
        </div>
        <div>
            <label for="to">To:</label>
            <input type="date" name="to" id="to" v-model='to' @change="goToPageOne">
        </div>
        <div>
            <h1>Totals</h1>
            <label for="totalKcal">Calories</label>
            <input type="text" name="totalKcal" id="totalKcal" :value="totalKcal" disabled>

            <label for="totalFat">Fat</label>
            <input type="text" name="totalFat" id="totalFat" :value="totalFat" disabled>

            <label for="totalProtein">Protein</label>
            <input type="text" name="totalProtein" id="totalProtein" :value="totalProtein" disabled>

            <label for="totalCarbohydrate">Carbohydrate</label>
            <input type="text" name="totalCarbohydrate" id="totalCarbohydrate" :value="totalCarbohydrate" disabled>

            <label for="totalPotassium">Potassium</label>
            <input type="text" name="totalPotassium" id="totalPotassium" :value="totalPotassium" disabled>
        </div>
        <div>
            <h1>Averages</h1>
            <label for="averageKcal">Calories</label>
            <input type="text" name="averageKcal" id="averageKcal" :value="averageKcal" disabled>

            <label for="averageFat">Fat</label>
            <input type="text" name="averageFat" id="averageFat" :value="averageFat" disabled>

            <label for="averageProtein">Protein</label>
            <input type="text" name="averageProtein" id="averageProtein" :value="averageProtein" disabled>

            <label for="averageCarbohydrate">Carbohydrate</label>
            <input type="text" name="averageCarbohydrate" id="averageCarbohydrate" :value="averageCarbohydrate" disabled>

            <label for="averagePotassium">Potassium</label>
            <input type="text" name="averagePotassium" id="averagePotassium" :value="averagePotassium" disabled>
        </div>

        <div class='flex justify-between'>
            <h1>Log Entries</h1>
            <button @click="add">Add Log Entry</button>
        </div>
        <table>
            <tr>
                <th>Date / Time</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>KCal</th>
                <th>Fat</th>
                <th>Protein</th>
                <th>Carbohydrate</th>
                <th>Potassium</th>
                <th>Actions</th>
            </tr>
            <tr v-for="logentry in logentries.data" :key="logentry.id">
                <td>{{logentry.consumed_at}}</td>
                <td>{{logentry.description}}</td>
                <td>{{logentry.quantity}}</td>
                <td>{{logentry.kcal}}</td>
                <td>{{logentry.fat}}</td>
                <td>{{logentry.protein}}</td>
                <td>{{logentry.carbohydrate}}</td>
                <td>{{logentry.potassium}}</td>
                <td><button @click="edit(logentry)">Edit</button><button @click="destroy(logentry)">Delete</button></td>
            </tr>
        </table>
        <div>
            <button @click="goToPageOne">First</button>
            <button @click="previousPage">Previous</button>
            <button @click="nextPage">Next</button>
            <button @click="lastPage">Last</button>
        </div>
    </div>
</template>

<script>
export default {
    props:{
        logentries: Object,
        page: Number,
        totalKcal: Number,
        totalFat: Number,
        totalProtein: Number,
        totalCarbohydrate: Number,
        totalPotassium: Number,
        averageKcal: Number,
        averageFat: Number,
        averageProtein: Number,
        averageCarbohydrate: Number,
        averagePotassium: Number,
    },
    data(){
        return {
            today: '',
            from: '',
            to: '',
            myhello:''
        }
    },
    mounted(){
        this.today = new Date()
        this.to = this.today.toISOString().substr(0,10);

        this.lastWeek = new Date();
        this.lastWeek.setDate(this.lastWeek.getDate()-7);
        this.from = this.lastWeek.toISOString().substr(0,10);
    },
    methods:{
        add () {
            let url = `${this.$route("logentries.create")}`;
            console.log(url);
            this.$inertia.visit(url);
        },
        edit(logentry) {
            let url = `${this.$route("logentries.edit", logentry)}`;
            this.$inertia.visit(url);
        },
        destroy(logentry) {
            let url = this.$route("logentries.destroy", logentry.id);
            this.$inertia.delete(url);
        },
        goToPageOne(){
            this.goToPage(1);
        },
        previousPage(){
            if(this.page>1) this.goToPage(this.page-1);
        },
        nextPage(){
            console.log('next page');
            console.log('this.page', this.page);
            console.log('this.logentries.meta.last_page', this.logentries.meta.last_page);
            if(this.page<this.logentries.meta.last_page) this.goToPage(this.page+1);
        },
        lastPage(){
            this.goToPage(this.logentries.meta.last_page);
        },
        goToPage(page){
            let url = `${this.$route("logentries.index")}`;
            url += `?from=${this.from}`;
            url += `&to=${this.to}`;
            this.$inertia.visit(url, {
                data:{
                    'page':page
                },
                preserveState: true,
                preserveScroll: true,
            });
        },
    }
}
</script>
