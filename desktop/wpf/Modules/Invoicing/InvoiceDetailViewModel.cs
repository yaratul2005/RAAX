using System.Threading.Tasks;
using ErpSample.Common;

namespace ErpSample.Modules.Invoicing
{
    public class InvoiceDetailViewModel : DetailViewModelBase<InvoiceHeader>
    {
        private readonly IInvoiceRepository _repository;

        public RelayCommand AddLineCommand { get; private set; }
        public RelayCommand RemoveLineCommand { get; private set; }

        public InvoiceDetailViewModel(InvoiceHeader item, bool isNew, IInvoiceRepository repository)
            : base(item, isNew)
        {
            _repository = repository;
            AddLineCommand = new RelayCommand(param => Item.Lines.Add(new InvoiceLine()));
            RemoveLineCommand = new RelayCommand(param => Item.Lines.Remove(param as InvoiceLine), param => param is InvoiceLine);
        }

        protected override Task PersistAsync(InvoiceHeader item)
        {
            return _repository.SaveInvoiceAsync(item);
        }
    }
}
