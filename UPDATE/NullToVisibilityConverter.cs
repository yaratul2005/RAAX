using System;
using System.Globalization;
using System.Windows;
using System.Windows.Data;

namespace ErpSample.Common
{
    /// <summary>Collapses the docked detail panel when nothing is being edited.</summary>
    public class NullToVisibilityConverter : IValueConverter
    {
        public object Convert(object value, Type targetType, object parameter, CultureInfo culture)
            => value == null ? Visibility.Collapsed : Visibility.Visible;

        public object ConvertBack(object value, Type targetType, object parameter, CultureInfo culture)
            => throw new NotSupportedException();
    }
}
