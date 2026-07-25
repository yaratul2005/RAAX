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
        {
            return _repository.GetInvoicesAsync(SearchText);
        }

        protected override Task DeleteItemAsync(InvoiceHeader item)
        {
            return _repository.DeleteInvoiceAsync(item);
        }
    }
}
