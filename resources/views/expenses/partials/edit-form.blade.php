<x-expense-form 
    :expense="$expense"
    :categories="$categories"
    :action="route('expenses.update', $expense)"
    method="POST"
    mode="edit"
/>
