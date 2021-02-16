using NUnit.Framework;
using OpenQA.Selenium;
using OpenQA.Selenium.Chrome;
using OpenQA.Selenium.Support.UI;
using System;
using System.Threading;

namespace SeleniumTests {
    static class NewAccount {

        static IWebDriver browser;

        public static void Setup() {

            var options = new ChromeOptions();

            options.AcceptInsecureCertificates = true;
            options.AddArgument("--headless");
            browser = new ChromeDriver(options);
        }

        [Test]
        public static void PasswordsDontMatch() {
            Setup();

            browser.Navigate().GoToUrl("https://localhost");
            new WebDriverWait(browser, TimeSpan.FromSeconds(5)).Until(c => c.FindElement(By.CssSelector(".Login")));

            TestHelper.ClickElement(browser, "css", "p");
            Console.WriteLine("Clicked on 'Create new account'");

            string redirectedURL = browser.Url;
            string expectedRedirect = "https://localhost/KPMessenger/site/createNewAccount.php";

            if (redirectedURL == expectedRedirect) {

                TestHelper.SetText(browser, "css", ".username_txt", "Test 4");
                Console.WriteLine("Username Entered");
                TestHelper.SetText(browser, "name", "password", "test");
                Console.WriteLine("Password Entered");                
                TestHelper.SetText(browser, "name", "password-validate", "differentPass");
                Console.WriteLine("Wrong validation password entered");

                TestHelper.ClickElement(browser, "css", ".login_btn");
                Console.WriteLine("Submit button clicked");

                string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
                string expectedMessage = "The password fields were not the same";

                TestHelper.Assert(errorMessage, expectedMessage);

            } else {
                Assert.Fail();
            }



            browser.Close();
        }
    }
}
