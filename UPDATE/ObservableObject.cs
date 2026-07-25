using System.ComponentModel;
using System.Runtime.CompilerServices;

namespace ErpSample.Common
{
    /// <summary>
    /// Base class for anything that needs to notify the UI when a property changes.
    /// Every Model and every ViewModel in the app inherits from this (Models go through
    /// ValidatableModel below, which itself inherits from this).
    /// </summary>
    public abstract class ObservableObject : INotifyPropertyChanged
    {
        public event PropertyChangedEventHandler? PropertyChanged;

        protected void OnPropertyChanged([CallerMemberName] string? propertyName = null)
        {
            PropertyChanged?.Invoke(this, new PropertyChangedEventArgs(propertyName));
        }

        /// <summary>
        /// Sets a backing field and raises PropertyChanged only if the value actually
        /// changed. Returns true when it changed, so callers can chain extra logic
        /// (recalculate a total, re-validate a field, etc).
        /// </summary>
        protected bool SetProperty<T>(ref T field, T value, [CallerMemberName] string? propertyName = null)
        {
            if (Equals(field, value)) return false;
            field = value;
            OnPropertyChanged(propertyName);
            return true;
        }
    }
}
