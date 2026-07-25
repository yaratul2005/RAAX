using System.Collections.Generic;
using System.Threading.Tasks;
using ErpSample.Common;

namespace ErpSample.Modules.Invoicing
{
    public class InvoiceListViewModel : ListViewModelBase<InvoiceHeader>
    {
        private readonly IInvoiceRepository _repository;

        public InvoiceListViewModel(IInvoiceRepository repository)
        {
            _repository = repository;
        }

        protected override Task<IEnumerable<InvoiceHeader>> LoadItemsAsync()
            => _repository.GetAllAsync();

        protected override Task DeleteItemAsync(InvoiceHeader item)
            => _repository.DeleteAsync(item.InvoiceNumber);
    }

    // Swap the implementation for EF Core / Dapper / your actual data layer.
    // The view models above never need to know which one you picked.
    public interface IInvoiceRepository
    {
        Task<IEnumerable<InvoiceHeader>> GetAllAsync();
        Task SaveAsync(InvoiceHeader header);
        Task DeleteAsync(string invoiceNumber);
    }
}
