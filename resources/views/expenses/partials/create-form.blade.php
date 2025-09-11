<x-expense-form 
    :categories="$categories"
    :action="route('expenses.store')"
    method="POST"
    mode="create"
/>
