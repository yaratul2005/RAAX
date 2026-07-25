using System.Collections.Generic;
using System.Threading.Tasks;

namespace ErpSample.Modules.Invoicing
{
    public interface IInvoiceRepository
    {
        Task<IEnumerable<InvoiceHeader>> GetInvoicesAsync(string searchPattern);
        Task SaveInvoiceAsync(InvoiceHeader invoice);
        Task DeleteInvoiceAsync(InvoiceHeader invoice);
    }
}
