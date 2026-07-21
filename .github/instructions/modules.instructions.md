# Modular Monolith Separation Instructions

## applyTo: "modules//*.php"

* **Loose Coupling**: Never allow one module (e.g., Sales) to directly query the database tables or import the Eloquent models of another module (e.g., Inventory).

* **API & Contract Boundaries**: Inter-module communication must take place via registered service contracts (defined under `app/Contracts/`) or asynchronously by dispatching domain events via Horizon queues.

* **Standardized Validation**: All custom request validations must extend `App\Http\Requests\BaseRequest` to enforce the global 422 JSON error format.
