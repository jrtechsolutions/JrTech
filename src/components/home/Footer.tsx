import { motion } from 'framer-motion';
import { 
  Linkedin, 
  Instagram, 
  Github, 
  Mail, 
  Phone, 
  MapPin,
  ArrowUp,
  Heart
} from 'lucide-react';

const footerLinks = {
  services: [
    { name: 'Infraestrutura de TI', href: '#servicos' },
    { name: 'Segurança Digital', href: '#servicos' },
    { name: 'Cloud Computing', href: '#servicos' },
    { name: 'Suporte Técnico', href: '#servicos' },
    { name: 'Desenvolvimento Web', href: '#servicos' },
  ],
  company: [
    { name: 'Sobre Nós', href: '#sobre' },
    { name: 'Portfólio', href: '#portfolio' },
    { name: 'Contato', href: '#contato' },
  ],
};

const socialLinks = [
  { icon: Linkedin, href: '#', label: 'LinkedIn' },
  { icon: Instagram, href: '#', label: 'Instagram' },
  { icon: Github, href: '#', label: 'GitHub' },
];

export default function Footer() {
  const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const scrollToSection = (href: string) => {
    const element = document.querySelector(href);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth' });
    }
  };

  return (
    <footer className="relative bg-slate-950 border-t border-slate-800/50">
      {/* Background Gradient */}
      <div className="absolute inset-0 bg-gradient-to-t from-blue-950/20 to-transparent pointer-events-none" />

      <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Main Footer Content */}
        <div className="py-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
          {/* Brand Column */}
          <div className="lg:col-span-1">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              className="mb-6"
            >
              <a href="#home" onClick={(e) => { e.preventDefault(); scrollToSection('#home'); }} className="flex items-center gap-3">
                <div className="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                  <span className="text-white font-bold text-lg">JR</span>
                </div>
                <div>
                  <span className="text-white font-semibold">Technology</span>
                  <span className="text-blue-400 font-light ml-1">Solutions</span>
                </div>
              </a>
            </motion.div>
            <p className="text-slate-400 text-sm leading-relaxed mb-6">
              Transformando negócios através de soluções tecnológicas 
              inovadoras, seguras e escaláveis.
            </p>

            {/* Social Links */}
            <div className="flex gap-3">
              {socialLinks.map((social) => (
                <a
                  key={social.label}
                  href={social.href}
                  aria-label={social.label}
                  className="p-2.5 bg-slate-800/50 hover:bg-blue-600 rounded-lg text-slate-400 hover:text-white transition-all duration-300"
                >
                  <social.icon className="w-5 h-5" />
                </a>
              ))}
            </div>
          </div>

          {/* Services Column */}
          <div>
            <h4 className="text-white font-semibold mb-6">Serviços</h4>
            <ul className="space-y-3">
              {footerLinks.services.map((link) => (
                <li key={link.name}>
                  <a
                    href={link.href}
                    onClick={(e) => { e.preventDefault(); scrollToSection(link.href); }}
                    className="text-slate-400 hover:text-blue-400 text-sm transition-colors"
                  >
                    {link.name}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          {/* Company Column */}
          <div>
            <h4 className="text-white font-semibold mb-6">Empresa</h4>
            <ul className="space-y-3">
              {footerLinks.company.map((link) => (
                <li key={link.name}>
                  <a
                    href={link.href}
                    onClick={(e) => { e.preventDefault(); scrollToSection(link.href); }}
                    className="text-slate-400 hover:text-blue-400 text-sm transition-colors"
                  >
                    {link.name}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact Column */}
          <div>
            <h4 className="text-white font-semibold mb-6">Contato</h4>
            <ul className="space-y-4">
              <li>
                <a
                  href="tel:+5511973802744"
                  className="flex items-center gap-3 text-slate-400 hover:text-blue-400 text-sm transition-colors"
                >
                  <Phone className="w-4 h-4" />
                  (11) 97380-2744
                </a>
              </li>
              <li>
                <a
                  href="mailto:contato@jrtechnologysolutions.com.br"
                  className="flex items-center gap-3 text-slate-400 hover:text-blue-400 text-sm transition-colors"
                >
                  <Mail className="w-4 h-4" />
                  contato@jrtechnologysolutions.com.br
                </a>
              </li>
              <li>
                <span className="flex items-center gap-3 text-slate-400 text-sm">
                  <MapPin className="w-4 h-4" />
                  São Paulo, SP - Brasil
                </span>
              </li>
            </ul>
          </div>
        </div>

        {/* Bottom Bar */}
        <div className="py-6 border-t border-slate-800/50">
          <div className="flex flex-col sm:flex-row justify-between items-center gap-4">
            <p className="text-slate-500 text-sm flex items-center gap-1">
              © {new Date().getFullYear()} JR Technology Solutions. Feito com{' '}
              <Heart className="w-4 h-4 text-red-500 fill-red-500" /> no Brasil.
            </p>

            {/* Back to Top */}
            <button
              onClick={scrollToTop}
              className="flex items-center gap-2 text-slate-400 hover:text-blue-400 text-sm transition-colors group"
            >
              Voltar ao topo
              <ArrowUp className="w-4 h-4 group-hover:-translate-y-1 transition-transform" />
            </button>
          </div>
        </div>
      </div>
    </footer>
  );
}
